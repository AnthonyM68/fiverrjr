<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Service;
use App\Entity\Category;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;

use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\EventListener\AddUserFieldSubscriber;
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

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder
        //     // ->add('course_id')
        //     ->add('title', TextType::class, [
        //         'label' => 'Titre'
        //     ])
        //     ->add('description', TextareaType::class)
        //     ->add('price', NumberType::class, [
        //         'label' => 'Prix',
        //         'scale' => 2, // forcer l'affichage de 2 décimales
        //         'attr' => [
        //             'step' => 0.01, // permettre des incréments de centimes
        //         ],
        //     ])
        //     ->add('duration', IntegerType::class, [ 
        //         'label' => 'Durée'
        //     ])
        //     ->add('createDate', DateType::class, [
        //         'widget' => 'single_text',
        //         'attr' => [
        //             'style' => 'display:none', // Masquer le champ avec du CSS
        //         ]
        //     ])
        //     ->add('course', EntityType::class, [
        //         'class' => Course::class,
        //         'choice_label' => 'nameCourse',
        //         'multiple' => true,
        //         'attr' => [
        //             'class' => 'ui fluid search dropdown'
        //         ]
        //     ])
        //     // ->add('user', EntityType::class, [
        //     //     'class' => User::class,
        //     //     'choice_label' => 'id',
        //     // ])
        //     // ->add('orders', EntityType::class, [
        //     //     'class' => Order::class,
        //     //     'choice_label' => 'id',
        //     // ])
        //     ->add('Valider', SubmitType::class, [
        //         'attr' => [
        //             'class' => 'ui-button ui-widget ui-corner-all'
        //         ]
        //     ])
        //     ->addEventSubscriber(new AddUserFieldSubscriber($this->security)); // Ajouter le Subscriber ici

        // ;
        // $formModifier = function (FormInterface $form, ?Theme $theme = null): void {
        //     $categories = null === $theme ? [] : $theme->getCategories();

        //     $form->add('position', EntityType::class, [
        //         'class' => Category::class,
        //         'placeholder' => '',
        //         'choices' => $categories,
        //     ]);
        // };

        // $builder->addEventListener(
        //     FormEvents::PRE_SET_DATA,
        //     function (FormEvent $event) use ($formModifier): void {
        //         // this would be your entity, i.e. SportMeetup
        //         $data = $event->getData();

        //         $formModifier($event->getForm(), $data->getCategories());
        //     }
        // );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Service::class,
        ]);
    }
}