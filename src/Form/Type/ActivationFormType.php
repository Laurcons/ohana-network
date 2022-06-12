<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ActivationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $pronounList = [];
        foreach (User::getValidPronouns() as $val) {
            $pronounList[$val] = $val;
        }
        $builder
            ->add('username', TextType::class, ['help' => 'Your username will only be used to login.'])
            // ->add('password', PasswordType::class, [])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "The passwords must match.",
                'first_options' => [
                    'label' => "New password",
                    'help' => 'Please set a new password, that only you know. Minimum 6 characters.',
                ],
                'second_options' => [
                    'label' => "New password confirm",
                    'help' => "Confirm your new password."
                ]
            ])
            ->add('birthdate', DateType::class, ['widget' => 'single_text'])
            ->add('realName', TextType::class, ['help' => 'Please make sure that you start with your family name, then your preferred given name, then any other given names (if you want). Do not use dashes. Eg. Pricop Laurentiu Constantin'])
            ->add('nickname', TextType::class, ['help' => 'Set a nickname that will be used across the Network.'])
            ->add('pronouns', ChoiceType::class, [
                'help' => 'Your pronouns will be used across the Network when referring to you. Neo-pronouns are not supported.',
                'choices' => $pronounList
            ])
            ->add('submit', SubmitType::class, ['label' => "Finish registration"]);
    }
}