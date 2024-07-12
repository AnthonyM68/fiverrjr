<?php


namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

// Formualire de recherche de la page search.html.twig
class ServiceSearchFormType extends AbstractType
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
            ->add('search_term', ServiceSearchFormType::class, [
                'required' => true,
                'label' => 'Services...',
                 'attr' => ['placeholder' => 'Services...']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
