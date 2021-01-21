<?php

namespace App\Form;

use App\Entity\Publication;
use App\Entity\Science;
use App\Repository\ScienceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('science', EntityType::class, [
                'label'         => 'Science',
                'class'         => Science::class,
                'query_builder' => function (ScienceRepository $repository) {
                    return $repository
                        ->createQueryBuilder('s')
                        ->addOrderBy('s.title', 'ASC');
                },
            ])
            ->add('title', Type\TextType::class, [
                'label' => 'Titre',
            ])
            ->add('author', Type\TextType::class, [
                'label' => 'Auteur',
            ])
            ->add('description', Type\TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('content', Type\TextareaType::class, [
                'label' => 'Contenu',
                'required' => false, // Prevent TinyMCE bug :(
            ]);

        if (!$options['admin']) {
            return;
        }

        $builder
            ->add('publishedAt', Type\DateType::class, [
                'label'  => 'Date de publication',
                'widget' => 'single_text',
            ])
            ->add('validated', Type\CheckboxType::class, [
                'label' => 'Validée',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'admin'      => false,
                'data_class' => Publication::class,
            ]);
    }
}
