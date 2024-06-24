<?php 

namespace App\Form\EventListener;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddUserFieldServiceForm implements EventSubscriberInterface 
{
    private $security;

    public function __construct(Security $security)
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
        $user = $this->security->getUser();

        if ($user instanceof User) {
            $form->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'data' => $user,
            ]);
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $user = $this->security->getUser();

        if ($user instanceof User) {
            $data['user'] = $user->getId();
        }

        $event->setData($data);
    }
}