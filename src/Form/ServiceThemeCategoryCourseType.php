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
                // 'constraints' => [
                //     new NotBlank([
                //         'message' => 'Veuillez sélectionner un Thème',
                //     ]),
                // ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégories',
                'placeholder' => 'Choisissez une Catégorie',
                'mapped' => false, // Non mapped à service
                'attr' => ['class' => 'ui fluid search dropdown'],
                'required' => false,
                // 'constraints' => [
                //     new NotBlank([
                //         'message' => 'Veuillez sélectionner une catégorie',
                //     ]),
                // ],
            ])
            // ne pas indiquer de mapped 
            ->add('course', EntityType::class, [
                'class' => Course::class,
                'label' => 'Sous-catégories',
                'placeholder' => 'Choisissez une Sous-Catégorie',
                'attr' => ['class' => 'ui fluid search dropdown'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une Sous-catégorie',
                    ]),
                ],
            ]);
        // Recherche et remplis les champs appropriés (nécessaire si pas de js pour un rendu dynamique)
        // // Écouteurs d'événements pour les champs theme et category
        // $builder->get('theme')->addEventListener(
        //     FormEvents::POST_SUBMIT,
        //     function (FormEvent $event) {
        //         $form = $event->getForm();
        //         $theme = $form->getData();
        //         // On recherche les catégories appartenant à un thême
        //         $categories = $theme ? $theme->getCategories() : [];
        //         // Ajout dynamique du champ category après la sélection du thème
        //         $form->getParent()->add('category', EntityType::class, [
        //             'class' => Category::class,
        //             'choices' => $categories,
        //             'mapped' => false,
        //             'attr' => ['class' => 'ui fluid search dropdown']
        //         ]);
        //     }
        // );
        // $builder->get('category')->addEventListener(
        //     FormEvents::POST_SUBMIT,
        //     function (FormEvent $event) {
        //         $form = $event->getForm();
        //         $category = $form->getData();
        //         // On recherche les courses appartenant à une catégorie
        //         $courses = $category ? $category->getCourses() : [];
        //         // Ajout dynamique du champ course après la sélection de la catégorie
        //         $form->getParent()->add('course', EntityType::class, [
        //             'class' => Course::class,
        //             'choices' => $courses,
        //             'mapped' => true,
        //             'attr' => ['class' => 'ui fluid search dropdown']
        //         ]);
        //     }
        // );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
