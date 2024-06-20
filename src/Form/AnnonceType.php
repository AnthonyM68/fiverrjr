<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Category;
use Symfony\Component\Form\FormEvent;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\form;

class AnnonceType extends AbstractType
{
    // private $categoryRepository;
    // private $entityManager;

    // public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    // {
    //     $this->categoryRepository = $categoryRepository;
    //     $this->entityManager = $entityManager;
    // }


    // public function getCategory()
    // {
    //     $categories = $this->categoryRepository->findAll();
    //     return $categories;

    // }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'placeholder' => '',
                    'attr' => [
                        'class' => 'ui fluid search dropdown'
                    ]
                ]
            );


        $formModifier = function (FormInterface $form, ?Category $category = null): void {

            $courses = null === $category ? [] : $category->getAvailablePositions();

            $form->add('Namecourse', EntityType::class, [
                'class' => Course::class,
                'placeholder' => '',
                'choices' => $courses,
                'attr' => [
                    'class' => 'ui fluid search dropdown'
                ]
            ]);
        };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                if ($data && method_exists($data, 'getCategory')) {
                    $formModifier($event->getForm(), $data->getCategory());
                }
            }
        );

        $builder->get('category')->addEventListener(
            FormEvents::POST_SUBMIT,

            function (FormEvent $event) use ($formModifier): void {
                $category = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $category);
            }
        );
    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
