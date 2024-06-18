<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Course;
use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('course_id')
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class, [
                'scale' => 2, // forcer l'affichage de 2 décimales
                'attr' => [
                    'step' => 0.01, // permettre des incréments de centimes
                ],
            ])
            ->add('duration', IntegerType::class)
            ->add('createDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'choice_label' => 'nameCourse',
                'multiple' => true,
                'attr' => [
                    'class' => 'ui fluid search dropdown'
                ]
            ])
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('orders', EntityType::class, [
            //     'class' => Order::class,
            //     'choice_label' => 'id',
            // ])
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'ui-button ui-widget ui-corner-all'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
