<?php

namespace App\Form;

use App\Entity\ServiceItem;
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

use Symfony\Component\Form\Extension\Core\Type\FileType;

class ServiceItemType extends AbstractType
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
            // Selecteur de sous-catégorie
            // Affiche les 3 formulaires Theme, Category, Course
            ->add('course', ServiceThemeCategoryCourseType::class, [
                'mapped' => false, // Nous ne voulons pas mapper directement sur l'entité Service
            ])


            
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'ui fluid input'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Déscription',
                'attr' => [
                    'class' => 'ui fluid input'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'scale' => 2, // forcer l'affichage de 2 décimales
                'attr' => [
                    'step' => 0.01, // permettre des incréments de centimes
                ]
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée',
                'attr' => [
                    'class' => 'ui fluid input'
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image de service',
                'attr' => [
                    'class' => 'ui input',
                ],
                'required' => false,
                'mapped' => false
            ])
            ->add('createDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false, // Non requis
                'attr' => [
                    'style' => 'display:none', // Masquer le champ 
                ],
                'label' => false, // masquer le label
                'mapped' => false, // Ne pas mapper ce champ avec les données du formulaire
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
                'attr' => [
                    'class' => 'ui-button ui-widget ui-corner-all'
                ]
            ])
            // Écouteur pour ajouter l'ID utilisateur avant de persister
            ->addEventSubscriber(new AddUserFieldServiceForm($this->security))
            // Écouteur pour ajouter la date de création avant de persister
            ->addEventSubscriber(new AddCreateDateFiledServiceForm());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceItem::class,
        ]);
    }
}
