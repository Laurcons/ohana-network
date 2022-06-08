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
            $this->addFlash("notice", "pushed data " . json_encode($formData));
            return $this->redirectToRoute('polls');
        }

        return $this->renderForm('polls/new.html.twig', [
            'form' => $form
        ]);
    }
}
