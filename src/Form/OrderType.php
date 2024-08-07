<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Order;
use App\Form\EventListener\AddUserField;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use App\EventListener\AddDateOrderFiledOrderForm;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class OrderType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('serviceId', IntegerType::class, [
                'label' => 'Service Id',
                'label' => false, // masquer le label
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ]
            ])
            ->add('nameCourse', TextType::class, [
                'label' => 'Nom de sous-catégorie',
                'label' => false, // masquer le label
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ]
            ])
            // Écouteur pour ajouter l'ID utilisateur avant de persister
            ->addEventSubscriber(new AddUserField($this->security))
            // Écouteur pour ajouter la date de création avant de persister
            ->addEventSubscriber(new AddDateOrderFiledOrderForm());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
