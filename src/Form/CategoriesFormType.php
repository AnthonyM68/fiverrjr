<?php

namespace App\Form;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Category;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'placeholder' => 'Choisissez un Thême',
                'attr' => [
                    'class' => 'ui fluid search dropdown'
                ]
            ]);

            $formModifier = function (FormInterface $form, ?Theme $theme = null): void {
                if (null !== $theme) {
                   
                    $categories = $theme->getCategories();
    
                    $form->remove('category'); 
    
                    $form->add('NameCategory', EntityType::class, [
                        'class' => Category::class,
                        'placeholder' => 'Choisissez une catégorie',
                        'label' => 'Catégories',
                        'choices' => $categories,
                        'attr' => [
                            'class' => 'ui fluid search dropdown'
                        ]
                    ]);
                } else {
                 
                    $form->remove('NameCategory');
    
                    $form->add('NameCategory', EntityType::class, [
                        'class' => Category::class,
                        'placeholder' => 'En attente d\'un choix theme',
                        'label' => 'Catégories',
                        'choices' => [],
                        'attr' => [
                            'class' => 'ui fluid search dropdown'
                        ]
                    ]);
                }
            };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void {
                $data = $event->getData();
                if ($data && method_exists($data, 'getTheme')) {
                    $formModifier($event->getForm(), $data->getTheme());
                }
            }
        );

        $builder->get('theme')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier): void {
                $theme = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $theme);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
