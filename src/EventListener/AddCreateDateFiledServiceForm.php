<?php


namespace App\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Service;

class AddCreateDateFiledServiceForm
{
    // public function getSubscribedEvents()
    // {
    //     return [
    //         Events::prePersist,
    //     ];
    // }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Service) {
            $entity->setCreateDate(new \DateTime());
        }
    }
}
