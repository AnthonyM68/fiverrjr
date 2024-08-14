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
            // Input du terme a rechercher
            ->add($key, TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Services',
                    'class' => 'prompt ' . $options['id_suffix']
                ],
                'label' => false
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
