<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Formulaire de recherche de la navbar ( View navabar.html.twig )
class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $key = 'search_term_' . $options['id_suffix'];
        $tokenId = 'search_form_' . $options['id_suffix'] . '_token';

        $builder
            // Input du terme a rechercher
            ->add($key, TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Services',
                    'class' => 'prompt ' . $options['id_suffix'], 
                    'id' => $key
                ],
                'label' => false
            ])
            ->add($tokenId, HiddenType::class, [
                'data' => $options['csrf_token'],
                'attr' => [
                    'id' => $tokenId // Ajout de l'ID unique pour le token
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_label' => null,
            'csrf_token' => null,
            'search_table' => 'theme',// Point d'entrée de notre recherche
            'id_suffix' => 'default'
        ]);
    }
}