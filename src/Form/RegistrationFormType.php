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
            ->add('username', TextType::class, [])
            ->add('email', EmailType::class, [])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                // RGPD
                'constraints' => [
                    new IsTrue([
                        'message' => 'S\'ìl vous plait, vous devez accepter nos conditions.',
                    ]),
                ],
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => true,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => [
                    'class' => 'password-field'
                ]],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe']

            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Développeur' => 'ROLE_DEVELOPER',
                    'Entrepreneur' => 'ROLE_CLIENT',
                ],
                'attr' => [
                    'class' => 'ui checkbox checkbox-container',
                ],
                'multiple' => true,
                'label' => false,
                'data' => []
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
