<?php

namespace App\Form;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Category;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceThemeCategoryCourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'label' => 'Thèmes',
                'placeholder' => 'Choisissez un Thème',
                'mapped' => false, // Non mapped à service
                'attr' => ['class' => 'ui fluid search dropdown'],
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégories',
                'placeholder' => 'Choisissez une Catégorie',
                'mapped' => false, // Non mapped à service
                'attr' => ['class' => 'ui fluid search dropdown'],
                'required' => false,
            ])
            // ne pas indiquer de mapped 
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'label' => 'Sous-catégories',
                'placeholder' => 'Choisissez une Sous-Catégorie',
                'attr' => ['class' => 'ui fluid search dropdown']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
