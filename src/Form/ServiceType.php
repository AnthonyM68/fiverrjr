<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use App\Form\ServiceThemeCategoryCourseType;
use Symfony\Component\Form\FormBuilderInterface;

use App\Form\EventListener\AddUserFieldServiceForm;
use App\EventListener\AddCreateDateFiledServiceForm;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ServiceType extends AbstractType
{
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }
    private $entityManager;


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('course', ServiceThemeCategoryCourseType::class, [
                'mapped' => false, // Nous ne voulons pas mapper directement sur l'entité Service
            ])
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'ui fluid input'
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2, // forcer l'affichage de 2 décimales
                'attr' => [
                    'step' => 0.01, // permettre des incréments de centimes
                ],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée',
                'attr' => [
                    'class' => 'ui fluid input'
                ]
            ])
            ->add('createDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false, // Champ optionnel
                'attr' => [
                    'style' => 'display:none', // Masquer le champ avec du CSS
                ],
                'label' => false, // Optionnel : masquer le label si nécessaire
                'mapped' => false, // Ne pas mapper ce champ avec les données du formulaire
            ])
            ->add('valider', SubmitType::class, [
                'attr' => [
                    'class' => 'ui-button ui-widget ui-corner-all'
                ]
            ])
            // Écouteur pour ajouter l'utilisateur
            ->addEventSubscriber(new AddUserFieldServiceForm($this->security))
            // Écouteur pour ajouter la date de création
            ->addEventSubscriber(new AddCreateDateFiledServiceForm());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
