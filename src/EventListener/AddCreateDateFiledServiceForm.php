<?php

namespace App\EventListener;

use App\Entity\Service;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AddCreateDateFiledServiceForm implements EventSubscriberInterface
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // On vérifie si l'objet est une instance de Service
        if ($entity instanceof Service) {
            $entity->setCreateDate(new \DateTime());
        }

        $user = $entity->getUser();
        if ($user === null) {
            throw new \Exception('L\'utilisateur ne peut pas être nul');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }
}
