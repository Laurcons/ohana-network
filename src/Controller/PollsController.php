<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Entity\PollResponse;
use App\Form\Type\PollDeleteType;
use App\Repository\PollRepository;
use App\Repository\PollResponseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Mrsuh\JsonValidationBundle\JsonValidator\JsonValidator;

/**
 * @Route("/polls")
 */
class PollsController extends AbstractController
{
    /**
     * @Route("/", name="polls")
     */
    public function index(
        PollRepository $pollRepo
    ): Response
    {
        $allPolls = $pollRepo->findAll();

        return $this->render('polls/index.html.twig', [
            'polls' => $allPolls    
        ]);
    }

    /**
     * @Route("/new", name="polls_new")
     */
    public function new(
        Request $request,
        ManagerRegistry $doctrine,
        JsonValidator $validator
    ): Response
    {
        $formData = [];
        $form = $this->createFormBuilder($formData)
            ->add('dataJson', HiddenType::class)
            ->add('submit', SubmitType::class, ['label' => "Create poll"])
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $validator->validate($formData["dataJson"], "Schema/PollSchema.json");
            if (!empty($validator->getErrors())) {
                throw $this->createAccessDeniedException("Invalid JSON supplied.");
            }
            $data = json_decode($formData["dataJson"], true);
            $poll = new Poll();
            $poll
                ->setTitle($data["title"])
                ->setDescription($data["description"])
                ->setAnswers($data["answers"])
                ->setAuthor($this->getUser())
                ->setOptions([
                    "answersType" => $data["answersType"],
                    "resultsMode" => $data["resultsMode"],
                ]);
            $manager = $doctrine->getManager();
            $manager->persist($poll);
            $manager->flush();
            $this->addFlash("notice", "Poll successfully created.");
            return $this->redirectToRoute('polls');
        }

        return $this->renderForm('polls/new.html.twig', [
            'form' => $form
        ]);
    }

    /**
    * @Route("/{id}/edit", name="polls_edit")
    */
    public function edit(
        int $id,
        Request $request,
        ManagerRegistry $doctrine,
        PollRepository $pollRepo,
        JsonValidator $validator
    ) {
        $poll = $pollRepo->find($id);
        if ($poll === null) 
            throw $this->createNotFoundException("Poll not found.");
        if ($poll->getAuthor() !== $this->getUser())
            throw $this->createAccessDeniedException("You can only edit your own polls.");
        $formData = [
            "dataJson" => json_encode([
                "title" => $poll->getTitle(),
                "description" => $poll->getDescription(),
                "answersType" => $poll->getOptions()["answersType"],
                "resultsMode" => $poll->getOptions()["resultsMode"],
                "answers" => $poll->getAnswers()
            ])
        ];
        $editForm = $this->createFormBuilder($formData)
            ->add('dataJson', HiddenType::class)
            ->add('submit', SubmitType::class, ['label' => "Edit poll"])
            ->getForm();
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $formData = $editForm->getData();
            $validator->validate($formData["dataJson"], "Schema/PollSchema.json");
            if (!empty($validator->getErrors())) {
                throw $this->createAccessDeniedException("Invalid JSON supplied.");
            }
            $data = json_decode($formData["dataJson"], true);
            $poll
                ->setTitle($data["title"])
                ->setDescription($data["description"])
                ->setOptions([
                    "answersType" => $data["answersType"],
                    "resultsMode" => $data["resultsMode"],
                ]);
            // disallow answer changing if there are respondents
            if (count($poll->getPollResponses()) === 0)
                $poll->setAnswers($data["answers"]);
            $manager = $doctrine->getManager();
            $manager->flush();
            $this->addFlash("notice", "Poll successfully updated.");
            return $this->redirectToRoute('polls_vote', [ "id" => $id ]);
        }

        $deleteForm = $this->createForm(PollDeleteType::class);
        $deleteForm->handleRequest($request);
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            $manager = $doctrine->getManager();
            $manager->remove($poll);
            $manager->flush();
            $this->addFlash("notice", "Poll deleted successfully.");
            return $this->redirectToRoute("polls");
        }
        
        return $this->renderForm('polls/edit.html.twig', [
            "editForm" => $editForm,
            "deleteForm" => $deleteForm,
            "poll" => $poll
        ]);
    }

    /**
     * @Route("/{id}", name="polls_vote")
     */
    public function vote(
        int $id,
        Request $request,
        PollRepository $pollRepo,
        PollResponseRepository $pollResponseRepo,
        ManagerRegistry $doctrine
    ) {
        $poll = $pollRepo->find($id);
        if ($poll === null)
            throw $this->createNotFoundException("Poll not found.");
        $pollResponse = $pollResponseRepo->findOneBy([
            "poll" => $poll,
            "user" => $this->getUser()
        ]);
        $allResponses = $pollResponseRepo->findBy([
            "poll" => $poll
        ]);

        $formData = $pollResponse !== null ? [
            "answer" => $pollResponse->getResponses()
        ] : [];
        $form = $this->createFormBuilder($formData);
        $form->add("answer", ChoiceType::class, [
            "expanded" => true,
            "disabled" => $pollResponse !== null,
            "label" => $poll->getTitle(),
            "help" => $poll->getDescription(),
            "multiple" => $poll->getOptions()["answersType"] === "multi_select",
            "choices" => array_flip(array_map(
                function($a) { return $a["text"]; },
                $poll->getAnswers()
            ))
        ]);
        if ($pollResponse === null)
            $form->add("submit", SubmitType::class, [ "label" => "Cast vote" ]);
        $form = $form->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($pollResponse !== null)
                throw $this->createAccessDeniedException("Cannot vote twice.");
            $formData = $form->getData();
            $pollResponse = new PollResponse();
            $pollResponse
                ->setPoll($poll)
                ->setUser($this->getUser())
                ->setResponses(
                    $poll->getOptions()["answersType"] === "multi_select" ?
                    $formData["answer"] :
                        [$formData["answer"]]
                );
            $manager = $doctrine->getManager();
            $manager->persist($pollResponse);
            $manager->flush();
            // reload page
            return $this->redirectToRoute('polls_vote', [ "id" => $id ]);
        }

        return $this->renderForm('polls/vote.html.twig', [
            "poll" => $poll,
            "hasOwnResponse" => $pollResponse !== null,
            "allResponses" => $allResponses,
            "form" => $form
        ]);
    }
}
