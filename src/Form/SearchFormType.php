<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Formulaire de la navbar

// Formulaire qui prend une table de recherche aléatoire et recherche un terme.
class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                'required' => true,
                'attr' => [
                    'class' => 'required full-width-input', // Ajout de classe ici si nécessaire
                ],
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
