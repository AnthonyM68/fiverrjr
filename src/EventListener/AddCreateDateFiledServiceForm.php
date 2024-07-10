<?php

namespace App\EventListener;

use App\Entity\ServiceItem;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

// Ajoute la date de création d'un Service 
class AddCreateDateFiledServiceForm implements EventSubscriberInterface
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        // On vérifie si l'objet est une instance de Service
        if ($entity instanceof ServiceItem) {
            $entity->setCreateDate(new \DateTime());
            $user = $entity->getUser();
            if ($user === null) {
                throw new \Exception('Utilisateur inconnu');
            }
        } 
    }
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }
}
