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
        $poll = new Poll();
        $poll->setAuthor($this->getUser());
        $poll->setAnswers([
            "Helo",
            "Hi"
        ]);
        
        $form = $this->createFormBuilder($poll)
            ->add('title', TextType::class, ['help' => "What are you polling? Summarize the question in a single sentence."])
            ->add('description', TextareaType::class, ['help' => "Additional (optional) explanations go here."])
            ->add('answers', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true
            ])
            ->add('submit', SubmitType::class, ['label' => "Create poll"])
            ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            $manager->persist($poll);
            $manager->flush();
            
            return $this->redirectToRoute('polls');
        }

        return $this->renderForm('polls/new.html.twig', [
            'form' => $form
        ]);
    }
}
