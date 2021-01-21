<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', Type\TextType::class, [
                'label' => 'Pseudo',
            ])
            ->add('message', Type\TextareaType::class, [
                'label' => 'Message',
            ]);

        if (!$options['admin']) {
            return;
        }

        $builder
            ->add('validated', Type\CheckboxType::class, [
                'label' => 'Validée',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'admin'      => false,
            'data_class' => Comment::class,
        ]);
    }
}
