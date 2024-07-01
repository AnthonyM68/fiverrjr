<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Appartenance catégorie',
                'choice_label' => 'nameCategory',
                'attr' => [
                    'class' => 'ui fluid search dropdown'
                ]
            ])
        ->add('nameCourse', TextType::class, [
            'label' => 'Nom de sous-catégorie',
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Soumettre',
            'attr' => [
                'class' => 'ui-button ui-widget ui-corner-all'
            ]
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
