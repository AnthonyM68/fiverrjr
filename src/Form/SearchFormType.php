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
        $builder
        // Nonm de table de recherche peu varier ( Axe d'amélioration )
        // ( NavbarController )
        // id: search_form_search_term
        
            // ->add('search_table', TextType::class, [
            //     'label' => 'Table',
            //     'label_attr' => [
            //         // On masque le champ de table de recherche
            //         // 'style' => 'display: none;'
            //     ],
            //     'attr' => [
            //         'readonly' => true,
            //         // 'style' => 'display:none'
            //     ],
            //     'data' => $options['search_table'],
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Veuillez sélectionner un Thème',
            //         ]),
            //     ]
            // ])
            // term a rechercher
            ->add('search_term', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Rechercher',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un Thème',
                    ]),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_label' => null,
            'search_table' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'search_item'
        ]);
    }
}
