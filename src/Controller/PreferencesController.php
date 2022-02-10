<?php

namespace App\Controller;

use App\Entity\User;
use App\DTO\ChangePasswordDTO;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PreferencesController extends AbstractController
{
    /**
     * @Route("/preferences", name="preferences")
     */
    public function index(
        ManagerRegistry $doctrine,
        UserRepository $userRepo,
        Request $request,
        FormFactoryInterface $formFactory,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        /** @var User */
        $currentUser = $this->getUser();

        $preferencesForm = $formFactory->createNamed('prefs', FormType::class, $currentUser)
            ->add('username', TextType::class, ['help' => 'Your username will only be used to login.'])
            ->add('birthdate', DateType::class, ['widget' => 'single_text'])
            ->add('realName', TextType::class, ['help' => 'Please make sure that you start with your family name, then your preferred given name, then any other given names (if you want). Do not use dashes. Eg. Pricop Laurentiu Constantin'])
            ->add('nickname', TextType::class, ['help' => 'Set a nickname that will be used across the Network.'])
            ->add('pronouns', TextType::class, ['help' => 'Your pronouns will be used across the Network when referring to you. Please make sure they\'re in the <code>he/him/his</code> format, where the first pronoun is in nominative form ("he does"), the second is in dative form ("i give him"), and the third is in genitive form ("this is his stuff").', 'help_html' => true])
            ->add("submit", SubmitType::class, ['label' => "Update preferences"]);
        $preferencesForm->handleRequest($request);

        $changePasswordDTO = new ChangePasswordDTO();
        $changePasswordDTO->setCurrentPasswordChecker(function(ChangePasswordDTO $dto) use ($currentUser, $hasher) {
            return $hasher->isPasswordValid($currentUser, $dto->getCurrentPassword());
        });
        $changePasswordForm = $formFactory->createNamed('chgPwd', FormType::class, $changePasswordDTO)
            ->add("currentPassword", PasswordType::class)
            ->add("newPassword", PasswordType::class)
            ->add("newPasswordConfirm", PasswordType::class)
            ->add("submit", SubmitType::class, ['label' => "Change password"]);
        $changePasswordForm->handleRequest($request);

        if ($preferencesForm->isSubmitted() && $preferencesForm->isValid()) {
            $doctrine->getManager()->flush();
            $this->addFlash('notice', "Preferences updated successfully.");
            return $this->redirectToRoute($request->get('_route'));
        }

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $hash = $hasher->hashPassword($currentUser, $changePasswordDTO->getNewPassword());
            $currentUser->setPassword($hash);
            $this->addFlash('notice', "Password updated successfully.");
            $doctrine->getManager()->flush();
        }

        return $this->renderForm('preferences/index.html.twig', [
            'preferences_form' => $preferencesForm,
            'change_password_form' => $changePasswordForm
        ]);
    }
}
