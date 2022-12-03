<?php

namespace App\Controller;
use App\Entity\SecretSantaAssignation;
use App\Repository\SecretSantaAssignationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secret-santa")
 */
class SecretSantaController extends AbstractController {
  /**
   * @Route("/", name="secretSanta")
   */
  public function index(
    SecretSantaAssignationRepository $ssaRepo
  ): Response
  {
    if ($ssaRepo->isSecretSantaStarted()) {
      return $this->redirectToRoute('secretSanta_view');
    } else {
      return $this->redirectToRoute('secretSanta_signup');
    }
  }

  /**
   * @Route("/signup", name="secretSanta_signup")
   */
  public function signUp(
    SecretSantaAssignationRepository $ssaRepo,
    ManagerRegistry $doctrine,
    FormFactoryInterface $formFactory,
    Request $request
  ): Response
  {
    if ($ssaRepo->isSecretSantaStarted())
      return $this->redirectToRoute('secretSanta');
    /** @var \App\Entity\User */
    $user = $this->getUser();
    $ssAsn = $ssaRepo->findBySender($user);
    $signedUp = true;
    if ($ssAsn === null) {
      $ssAsn = new SecretSantaAssignation();
      $signedUp = false;
    }
    $signupForm = $formFactory->createNamed('signup', FormType::class, $ssAsn)
      ->add('address', TextareaType::class, [
        'help' => 'Your full address, as you would write it on an envelope.',
        'required' => true,
      ])
      ->add('observations', TextareaType::class, ['help' => 'Any observations regarding shipping.', 'required' => false ])
      ->add('message', TextareaType::class, ['label' => 'Preferences', 'help' => 'Do you want to tell your Secret Santa something? Do it here!', 'required' => false])
      ->add('submit', SubmitType::class);

    $signupForm->handleRequest($request);
    if ($signupForm->isSubmitted() && $signupForm->isValid()) {
      $ssAsn->setSender($user);
      $manager = $doctrine->getManager();
      $manager->persist($ssAsn);
      $manager->flush();
      $this->addFlash('notice', 'Information updated!');
      $signedUp = true;
    }
    return $this->renderForm('secret_santa/signup.html.twig', [
      'signupForm' => $signupForm,
      'signedUp' => $signedUp,
      'isAdmin' => in_array('ROLE_ADMIN', $user->getRoles()),
    ]);
  }

  /**
   * @Route("/view", name="secretSanta_view")
   */
  public function view(
    SecretSantaAssignationRepository $ssaRepo,
    ManagerRegistry $doctrine,
    Request $request
  ): Response
  {
    if (!$ssaRepo->isSecretSantaStarted())
      return $this->redirectToRoute('secretSanta');
    /** @var \App\Entity\User */
    $user = $this->getUser();
    $mySsAsn = $ssaRepo->findBySender($user);
    // obtain the receiver's details
    $otherSsAsn = $ssaRepo->findBySender($mySsAsn->getReceiver());

    $statusChoices = [
      'Still thinking of a great present!',
      'Dropping your present at the post office soon!',
      'Your present is on its way!'
    ];
    $statusForm = $this->createFormBuilder($mySsAsn)
      ->add('customStatus', ChoiceType::class, [
        'choices' => array_combine($statusChoices, $statusChoices),
        'expanded' => true,
        'label' => false
      ])
      ->add('submit', SubmitType::class, ['label' => 'Change status'])
      ->getForm();

    $statusForm->handleRequest($request);
    if ($statusForm->isSubmitted() && $statusForm->isValid()) {
      $doctrine->getManager()->flush();
      $this->addFlash('notice', 'Status changed!');
    }

    return $this->renderForm('secret_santa/view.html.twig', [
      'otherSsAsn' => $otherSsAsn,
      'statusForm' => $statusForm
    ]);
  }

  /**
   * @Route("/randomize", methods={"POST"}, name="secretSanta_randomize")
   */
  public function performAssignations(
    ManagerRegistry $doctrine,
    SecretSantaAssignationRepository $ssaRepo
  ): Response
  {
    // priviledges check
    /** @var \App\Entity\User */
    $user = $this->getUser();
    if (!in_array('ROLE_ADMIN', $user->getRoles())) {
      throw $this->createNotFoundException();
    }
    // perform randomization!
    /** @var SecretSantaAssignation[] */
    $ssas = $ssaRepo->findAll();
    $currentSsa = $ssas[0];
    $remaining = count($ssas) - 1;
    $total = count($ssas);
    while ($remaining-- > 0) {
      $tries = $total + 10;
      // pick random which has no receiver yet
      do {
        $randPos = rand(0, $total - 1);
        if ($tries-- === 0)
          throw new \InvalidArgumentException();
      } while (!(
        $ssas[$randPos]->getId() !== $currentSsa->getId() &&
        $ssas[$randPos]->getReceiver() === null &&
        $randPos !== 0
      ));
      $ssas[$randPos]->setReceiver($currentSsa->getSender());
      $currentSsa = $ssas[$randPos];
    }
    // tie loose end
    $ssas[0]->setReceiver($currentSsa->getSender());
    $doctrine->getManager()->flush();
    return $this->redirectToRoute('secretSanta');
  }
}