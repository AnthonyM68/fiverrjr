<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;


class UserRegistrationListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        // On vérifie si l'objet est une instance de User
        if ($entity instanceof User) {
            $entity->setDateRegister(new \DateTime());
        }
    }
}