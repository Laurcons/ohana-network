<?php

namespace App\Controller;

use App\Entity\Poll;
use App\Repository\PollRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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
        ManagerRegistry $doctrine
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
            // TODO: Urgent JSON validation is needed here!
            $data = json_decode($formData["dataJson"], true);
            $poll = new Poll();
            $poll
                ->setTitle($data["title"])
                ->setDescription($data["description"])
                ->setAnswers($data["answers"])
                ->setAuthor($this->getUser())
                ->setOptions([
                    "answersType" => $data["answersType"]
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
        PollRepository $pollRepo
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
                "answers" => $poll->getAnswers()
            ])
        ];
        $form = $this->createFormBuilder($formData)
            ->add('dataJson', HiddenType::class)
            ->add('submit', SubmitType::class, ['label' => "Edit poll"])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            // TODO: Urgent JSON validation is needed here!
            $data = json_decode($formData["dataJson"], true);
            $poll
                ->setTitle($data["title"])
                ->setDescription($data["description"])
                ->setAnswers($data["answers"])
                ->setOptions([
                    "answersType" => $data["answersType"]
                ]);
            $manager = $doctrine->getManager();
            $manager->flush();
            $this->addFlash("notice", "Poll successfully updated.");
            return $this->redirectToRoute('polls_vote', [ "id" => $id ]);
        }
        
        return $this->renderForm('polls/edit.html.twig', [
            "form" => $form,
            "poll" => $poll
        ]);
    }

    /**
     * @Route("/{id}", name="polls_vote")
     */
    public function vote(
        int $id,
        Request $request,
        PollRepository $pollRepo
    ) {
        $poll = $pollRepo->find($id);
        if ($poll === null)
            throw $this->createNotFoundException("Poll not found.");
        return $this->render('polls/vote.html.twig', [
            "poll" => $poll
        ]);
    }
}
