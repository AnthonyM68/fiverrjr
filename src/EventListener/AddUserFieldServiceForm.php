<?php 

namespace App\Form\EventListener;

use App\Entity\User;
use App\Entity\Service;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddUserFieldServiceForm extends AbstractType implements EventSubscriberInterface 
{
    private $security;

    public function __construct(Security $security = null)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $service = $event->getData();

        if ($service instanceof Service) {
            // Ajouter un champ caché pour l'ID de l'utilisateur
            $form->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'data' => $this->security->getUser(),
               /* 'attr' => [
                    'style' => 'display:none', // Masquer le champ avec du CSS
                ],*/
                'mapped' => false, // Ne pas mapper ce champ avec les données du formulaire
            ]);
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $user = $this->security->getUser();

        if ($user instanceof User) {
            $data['user'] = $user;
        }

        $event->setData($data);
    }
}
