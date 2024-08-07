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
        // ne pouvant pas modifier l'id par symfony
        // on modifie le nom de champ de façon unique
        // pour avoir deux form rendu en même temps sans confusion
        // et pour garder le mode desktop /  mobile
        $key = 'search_term_' . $options['id_suffix'];
        $builder
            // term a rechercher
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
            'search_table' => 'theme',
            // 'csrf_protection' => true,
            // 'csrf_field_name' => '_token',
            // 'csrf_token_id'   => 'search_item',
            'id_suffix' => 'default'
        ]);
    }
}
