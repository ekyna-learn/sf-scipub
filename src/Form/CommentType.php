<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('publication', EntityType::class, [
                'label' => 'Publication',
                'class' => Publication::class,
                'query_builder' => function(PublicationRepository $repository) {
                    return $repository
                        ->createQueryBuilder('p')
                        ->addOrderBy('p.title', 'ASC');
                }
            ])
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
