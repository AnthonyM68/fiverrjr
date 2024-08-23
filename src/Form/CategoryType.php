<?php

namespace App\Form;

use App\Entity\Theme;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'label' => 'Appartenance Thême',
                'choice_label' => 'nameTheme',
                'attr' => [
                    'class' => 'ui fluid search dropdown'
                ]
            ])
            ->add('nameCategory', TextType::class, [
                'label' => 'Nom de Catégorie',
<<<<<<< HEAD
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'ui-button ui-widget ui-corner-all'
=======
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom de Catégorie',
                    ]),
>>>>>>> a5feb3db027be62ad942fe5c640558f052dbbba0
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
