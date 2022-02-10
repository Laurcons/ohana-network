<?php

namespace App\Controller;

use App\Entity\User;
use App\DTO\ResetPasswordDTO;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/resetPassword", name="resetPassword")
     */
    public function index(Request $request, ManagerRegistry $doctrine, UserRepository $userRepo, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User */
        $user = $this->getUser();
        if ($user->getStatus() !== User::STATUS_PASSWORD_RESET) {
            return $this->redirectToRoute("home");
        }

        $dto = new ResetPasswordDTO();

        $form = $this->createFormBuilder($dto)
            ->add("password", RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Your passwords must match.",
                'first_options' => [ 'label' => "Password" ],
                'second_options' => [ 'label' => "Password confirm" ],
            ])
            ->add("submit", SubmitType::class, ["label" => "Reset password"])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            // do password update
            $user = $userRepo->find($user->getId());
            $hash = $hasher->hashPassword($user, $dto->getPassword());
            $user->setPassword($hash);

            // status
            $user->setStatus(User::STATUS_ACTIVE);

            $doctrine->getManager()->flush();

            $this->addFlash('notice', "Your password has been reset successfully.");

            return $this->redirectToRoute('home');
        }
        
        return $this->renderForm('reset_password/index.html.twig', [
            'form' => $form
        ]);
    }
}
