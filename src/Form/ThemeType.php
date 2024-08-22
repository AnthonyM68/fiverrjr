<?php

namespace App\Form;

use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameTheme', TextType::class, [
<<<<<<< HEAD
                'label' => 'Nom de Thême',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'ui-button ui-widget ui-corner-all'
                ]
=======
                'label' => 'Nom de Thême'
>>>>>>> a5feb3db027be62ad942fe5c640558f052dbbba0
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Theme::class,
        ]);
    }
}
