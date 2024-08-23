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
        // N'ayant pas trouvé de solution pour modifier l'id selon les règles de symfony
        // j'ai opter pour un id_suffix fournit à la construction du formulaire
        // afin d'éviter tout conflit entre deux formulaire du même type
        $key = 'search_term_' . $options['id_suffix'];
        $builder
<<<<<<< HEAD
            ->add('search_table', TextType::class, [
                'label' => 'Table',
                'label_attr' => [
                    'style' => 'display: none;'
                ],
                'attr' => [
                    'readonly' => true,
                    'style' => 'display:none'
                ],
                'data' => $options['search_table'],
            ])
            ->add('search_term', TextType::class, [
                'label' => $options['search_label'],
=======
            // Input du terme a rechercher
            ->add($key, TextType::class, [
>>>>>>> a5feb3db027be62ad942fe5c640558f052dbbba0
                'required' => true,
                'attr' => [
                    'placeholder' => 'Services',
                    'class' => 'prompt ' . $options['id_suffix']
                ],
<<<<<<< HEAD
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'ui-button ui-widget ui-corner-all'
                ]
=======
                'label' => false
>>>>>>> a5feb3db027be62ad942fe5c640558f052dbbba0
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_label' => null,
            'search_table' => 'theme',// Point d'entrée de notre recherche
            'id_suffix' => 'default'
        ]);
    }
}