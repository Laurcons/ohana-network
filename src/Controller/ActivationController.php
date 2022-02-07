<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ActivationFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ActivationController extends AbstractController
{
    /**
     * @Route("/activation", name="activation")
     */
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher): Response
    {
        /** @var User */
        $user = $this->getUser();
        if ($user->getStatus() !== User::STATUS_NOT_ACTIVATED) {
            return $this->redirectToRoute("home");
        }
        
        $userId = $user->getId();

        $userRepo = $doctrine->getRepository(User::class);
        /** @var User */
        $user = $userRepo->find($userId);

        $form = $this->createForm(ActivationFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // set active status
            $user->setStatus(User::STATUS_ACTIVE);

            // hash password
            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            // add ROLE_ACTIVATED
            $roles = $user->getRoles();
            $roles[] = "ROLE_ACTIVATED";
            $roles = array_unique($roles);
            $user->setRoles($roles);

            $doctrine->getManager()->flush();

            $this->addFlash("notice", "You might need to log in again using your new username and password.");

            return $this->redirectToRoute('home'); // will be redirected to login anyway
        }

        return $this->renderForm('activation/index.html.twig', [
            'form' => $form
        ]);
    }
}
