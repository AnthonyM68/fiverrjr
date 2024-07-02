<?php

namespace App\Form\EventListener;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

// Ecouteur d'événément pour ajouter automatiquement l'id user à l'enregistrement d'un service
class AddUserFieldServiceForm implements EventSubscriberInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    // Méthode statique pour indiquer les événements auxquels cet écouteur s'abonne
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',   // Avant que les données soient définies dans le formulaire
            FormEvents::PRE_SUBMIT => 'preSubmit',     // Avant la soumission du formulaire
        ];
    }

    // Cette méthode est appelée avant de définir les données dans le formulaire
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();   // Récupère le formulaire lié à l'événement
        $user = $this->security->getUser();   // Récupère l'utilisateur actuellement authentifié

        // Vérifie si l'utilisateur est connecté et s'il s'agit d'une instance de la classe User
        if ($user instanceof User) {
            // Ajoute un champ "user" de type EntityType au formulaire
            $form->add('user', EntityType::class, [
                'class' => User::class,   // Entité associée au champ
                'choice_label' => 'id',   // Propriété de l'entité à afficher dans le champ (dans cet exemple, l'ID)
                'data' => $user,          // Valeur par défaut du champ, ici l'utilisateur actuel
                'attr' => [
                    'style' => 'display:none', // Masquer le champ avec du CSS
                ],
                'label' => false, // Désactiver l'affichage du label
            ]);
        }
    }

    // Cette méthode est appelée avant la soumission du formulaire
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();   // Récupère les données soumises
        $user = $this->security->getUser();   // Récupère l'utilisateur actuellement authentifié

        // Vérifie si l'utilisateur est connecté et s'il s'agit d'une instance de la classe User
        if ($user instanceof User) {
            // Modifie les données soumises pour inclure l'ID de l'utilisateur dans le champ "user"
            $data['user'] = $user->getId();
        }

        $event->setData($data);   // Définit les données modifiées pour l'événement
    }
}
