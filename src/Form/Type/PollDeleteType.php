<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class PollDeleteType extends AbstractType {

    public function getBlockPrefix()
    {
        return 'deleteform';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("submit", SubmitType::class, [
                "label" => "Continue with deletion :(",
                "attr" => [ "class" => "btn btn-sm btn-danger" ],
            ]);
    }
}