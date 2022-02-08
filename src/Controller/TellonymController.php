<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tellonym;
use App\Repository\UserRepository;
use App\Repository\TellonymRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/tellonym")
 */
class TellonymController extends AbstractController
{
    /**
     * @Route("/", name="tellonym")
     */
    public function index(
        TellonymRepository $tellonymRepo,
        UserRepository $userRepo,
        ManagerRegistry $doctrine,
        Request $request
    ): Response
    {
        $ownTellonyms = $tellonymRepo->findForUserOrderedUnhidden($this->getUser());
        $allUsers = $userRepo->findAll();

        $newTellonym = new Tellonym();
        $form = $this->createFormBuilder($newTellonym)
            ->add("destination", ChoiceType::class, [
                'choices' => $allUsers,
                'choice_value' => 'uuid',
                'choice_label' => function(User $user) { return $user->getNickname(); },
                'help' => "The sender (you) is not at all recorded, not even in the database. That means that not even Bubu can tell who sends which Tellonyms.",
            ])
            ->add("message", TextareaType::class, [
                'attr' => [
                    'placeholder' => "If you write like you would in an e-mail, with no Romanian diacritics, your anonymity will be in good hands. Also maybe don't make grammar mistakes?",
                    'rows' => "4"
                ],
                'help' => "There is a character limit of 5120 characters.",
            ])
            ->add("submit", SubmitType::class, ['label' => "Send Tellonym" ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $newTellonym
                ->setTimestamp(new \DateTime())
                ->setHidden(false)
                ->setSeen(false);

            $manager = $doctrine->getManager();
            $manager->persist($newTellonym);
            $manager->flush();

            return $this->redirectToRoute('tellonym'); // redirect to the GET version
        }

        return $this->renderForm('tellonym/index.html.twig', [
            'own_tellonyms' => $ownTellonyms,
            'form' => $form
        ]);
    }

    /**
     * @Route("/hide/{id}", name="tellonym_hide", methods={"POST"})
     */
    public function hide(
        int $id,
        TellonymRepository $tellonymRepo,
        ManagerRegistry $doctrine
    ): Response
    {
        $tellonym = $tellonymRepo->find($id);
        if (!$tellonym)
            throw $this->createNotFoundException('Tellonym not found');
        
        $tellonym->setHidden(true);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute("tellonym");
    }
}
