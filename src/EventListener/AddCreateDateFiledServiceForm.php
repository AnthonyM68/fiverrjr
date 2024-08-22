<?php

namespace App\EventListener;

use App\Entity\ServiceItem;

use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

// Ajoute la date de création d'un Service 
class AddCreateDateFiledServiceForm implements EventSubscriberInterface
{
    // Méthode appelée avant que l'entité soit persistée (enregistrée) dans la base de données
    public function prePersist(LifecycleEventArgs $args): void
    {
        // Récupère l'entité actuelle à partir des arguments de l'événement
        $entity = $args->getObject();

        // Vérifie si l'entité est une instance de ServiceItem
        if ($entity instanceof ServiceItem) {
            // Définit la date de création à la date et heure actuelles
            $entity->setCreateDate(new \DateTime());

            // Récupère l'utilisateur associé à l'entité
            $user = $entity->getUser();

            // Vérifie si l'utilisateur est nul (non défini)
            if ($user === null) {
                // Lance une exception si l'utilisateur est inconnu
                throw new \Exception('Utilisateur inconnu');
            }
        } 
    }

    // Méthode pour obtenir les événements auxquels cette classe est abonnée
    public static function getSubscribedEvents(): array
    {
        return [
            // S'abonne à l'événement prePersist, qui est déclenché avant que l'entité soit persistée
            Events::prePersist,
        ];
    }
}