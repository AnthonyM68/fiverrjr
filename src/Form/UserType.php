<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ],
                'label_attr' => [
                    'style' => 'display: none;' // Masquer le label
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])

            ->add('first_name', TextType::class, ['label' => 'Prénom'])
            ->add('last_name', TextType::class, ['label' => 'Nom'])
            ->add('phone_number', TextType::class, ['label' => 'Tel'])
            ->add('date_register', DateType::class, [
                'label' => 'Date Register',
                'label_attr' => [
                    'style' => 'display: none;' // Masquer le label
                ],
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ],
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image de profil',
                'label_attr' => [],
                'attr' => [
                    'class' => 'ui input',
                ],
                'required' => false,
                'mapped' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'label_attr' => []
            ])
            ->add('portfolio', TextType::class, [
                'label' => 'Portfolio',
                'required' => false,
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Bio'
            ])
            ->add('is_verified', CheckboxType::class, [
                'label' => 'Verified',
                'required' => false,
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ],
                'label_attr' => [
                    'style' => 'display: none;'
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'label_attr' => []
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
                'attr' => [
                    'class' => 'ui-button ui-widget ui-corner-all'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
