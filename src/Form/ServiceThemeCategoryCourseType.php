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
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceThemeCategoryCourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'placeholder' => 'Choose a Theme',
                'mapped' => false,
                'attr' => ['class' => 'ui fluid search dropdown']
            ])
            ->add('NameCategory', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Choose a Category',
                'choices' => [],
                'mapped' => false,
                'attr' => ['class' => 'ui fluid search dropdown']
            ])
            ->add('NameCourse', EntityType::class, [
                'class' => Course::class,
                'placeholder' => 'Choose a Course',
                'choices' => [],
                'attr' => ['class' => 'ui fluid search dropdown']
            ]);

        $builder->get('theme')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $theme = $form->getData();

                $categories = $theme ? $theme->getCategories() : [];

                $form->getParent()->add('category', EntityType::class, [
                    'class' => Category::class,
                    'placeholder' => 'Choose a Category',
                    'choices' => $categories,
                    'mapped' => false,
                    'attr' => ['class' => 'ui fluid search dropdown']
                ]);

                $form->getParent()->add('course', EntityType::class, [
                    'class' => Course::class,
                    'placeholder' => 'Choose a Course',
                    'choices' => [],
                    'attr' => ['class' => 'ui fluid search dropdown']
                ]);
            }
        );

        $builder->get('NameCategory')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $category = $form->getData();

                $courses = $category ? $category->getCourses() : [];

                $form->getParent()->add('course', EntityType::class, [
                    'class' => Course::class,
                    'placeholder' => 'Choose a Course',
                    'choices' => $courses,
                    'attr' => ['class' => 'ui fluid search dropdown']
                ]);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
