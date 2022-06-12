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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

        $pronounList = [];
        foreach (User::getValidPronouns() as $val) {
            $pronounList[$val] = $val;
        }
        if (!$currentUser->hasValidPronouns()) {
            $currentUser->setPronouns("they/them/their/theirs");
            $doctrine->getManager()->flush();
        }
        $preferencesForm = $formFactory->createNamed('prefs', FormType::class, $currentUser)
            ->add('username', TextType::class, ['help' => 'Your username will only be used to login.'])
            ->add('birthdate', DateType::class, ['widget' => 'single_text'])
            ->add('realName', TextType::class, ['help' => 'Please make sure that you start with your family name, then your preferred given name, then any other given names (if you want). Do not use dashes. Eg. Pricop Laurentiu Constantin'])
            ->add('nickname', TextType::class, ['help' => 'Set a nickname that will be used across the Network.'])
            ->add('pronouns', ChoiceType::class, [
                'help' => 'Your pronouns will be used across the Network when referring to you. Neo-pronouns can be added on demand.',
                'choices' => $pronounList
            ])
            ->add("submit", SubmitType::class, ['label' => "Update preferences"]);
        $preferencesForm->handleRequest($request);

        $changePasswordDTO = new ChangePasswordDTO();
        $changePasswordDTO->setCurrentPasswordChecker(function(ChangePasswordDTO $dto) use ($currentUser, $hasher) {
            return $hasher->isPasswordValid($currentUser, $dto->getCurrentPassword());
        });
        $changePasswordForm = $formFactory->createNamed('chgPwd', FormType::class, $changePasswordDTO)
            ->add("currentPassword", PasswordType::class)
            ->add("newPassword", RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "The passwords should match!",
                'first_options' => [ 'label' => "New password" ],
                'second_options' => [ 'label' => "New password confirm" ],
            ])
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
