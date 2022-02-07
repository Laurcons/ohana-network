<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ActivationFormType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['help' => 'Your username will only be used to login.'])
            ->add('password', PasswordType::class, ['help' => 'Please set a new password, that only you know.'])
            ->add('birthdate', DateType::class, ['widget' => 'single_text'])
            ->add('realName', TextType::class, ['help' => 'Please make sure that you start with your family name, then your preferred given name, then any other given names (if you want). Do not use dashes. Eg. Pricop Laurentiu Constantin'])
            ->add('nickname', TextType::class, ['help' => 'Set a nickname that will be used across the Network.'])
            ->add('pronouns', TextType::class, ['help' => 'Your pronouns will be used across the Network when referring to you. Please make sure they\'re in the <code>he/him/his</code> format, where the first pronoun is in nominative form ("he does"), the second is in dative form ("i give him"), and the third is in genitive form ("this is his stuff").', 'help_html' => true])
            ->add('submit', SubmitType::class, ['label' => "Finish registration"]);
    }
}