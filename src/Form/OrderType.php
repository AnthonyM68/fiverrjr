<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serviceId', IntegerType::class, [
                'label' => 'Service Id',
                'attr' => [
                    'class' => 'ui fluid input'
                ]
            ])
            ->add('userId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('dateOrder', DateType::class, [
                'label' => 'Date Commande',
                'label_attr' => [
                    'style' => 'display: none;' // Masquer le label
                ],
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ],
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateDelivery', DateType::class, [
                'label' => 'Date Facture',
                'label_attr' => [
                    'style' => 'display: none;' // Masquer le label
                ],
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ],
                'widget' => 'single_text',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
