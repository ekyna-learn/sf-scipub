<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class ConfirmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('confirm', CheckboxType::class, [
                'label'       => 'Confirmer la suppression ?',
                'constraints' => [
                    new IsTrue(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Supprimer',
                'attr'  => [
                    'class' => 'btn-danger',
                ],
            ]);
    }
}
