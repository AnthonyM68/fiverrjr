<?php

namespace App\Form;

use Assert\Email;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saisissez votre nom d\'utilisateur',
                    ]),
                    new Assert\Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 100,
                    ]),
                    
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saisissez votre Email',
                    ]),
                    new Assert\Email([
                        'message' => 'Veuillez saisir une adresse email valide.',
                    ])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'S\'ìl vous plait, vous devez accepter nos conditions.',
                    ]),
                ],
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Saisissez votre mot de passe',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 255,
                    ]),
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'Votre saisie ne correspond pas.',
                'options' => ['attr' => [
                    'class' => 'password-field'
                ]],
                'required' => true,
            ])
            // ->add('roles', ChoiceType::class, [
            //     'choices' => [
            //         'Développeur' => 'ROLE_DEVELOPER',
            //         'Entrepreneur' => 'ROLE_ENTERPRISE',
            //     ],
            //     'attr' => [
            //         'class' => 'ui checkbox checkbox-container',
            //     ],
            //     'multiple' => true,
            //     // 'expanded' => true,
            //     'label' => false,
            //     'data' => []
            // ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
