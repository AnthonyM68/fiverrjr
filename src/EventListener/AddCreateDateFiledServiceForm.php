<?php namespace App\EventListener;

use App\Entity\Service;
use Doctrine\ORM\Event\PrePersistEventArgs;

class AddCreateDateFiledServiceForm
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        // On vérifie si l'objet est une instance de Service
        if ($entity instanceof Service) {
            $entity->setCreateDate(new \DateTime());
        }
    }
}
