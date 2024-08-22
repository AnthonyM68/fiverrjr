<?php

namespace App\Service;

use LogicException;
use App\Entity\User;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAnonymizer extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    private function processOrderAndInvoiceAnonymization(User $user): void
    {
        $orders = $this->entityManager->getRepository(Order::class)->findBy(['user' => $user]);

        foreach ($orders as $order) {
            $order->setOrderTraceability([
                'order_id' => $order->getId(),
                'service_name' => $order->getService()->getName(),
                'amount' => $order->getAmount(),
            ]);

            $invoice = $order->getInvoice();
            if ($invoice) {
                $order->setInvoiceTraceability([
                    'invoice_id' => $invoice->getId(),
                    'issue_date' => $invoice->getIssueDate(),
                    'amount' => $invoice->getAmount(),
                ]);
            }

            // Dissocier l'utilisateur
            $order->setUser(null);
        }
    }
    private function anonymizeUserDetails(User $user): void
    {
        $uniqueString = uniqid('anonymized_', true);

        $user->setEmail($uniqueString . '@anonymized.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, $uniqueString));
        $user->setFirstName($uniqueString);
        $user->setLastName($uniqueString);
        $user->setPhoneNumber($uniqueString);
        $user->setUsername($uniqueString);
        $user->setPicture(null);
        $user->setCity($uniqueString);
        $user->setPortfolio($uniqueString);
        $user->setBio($uniqueString);
        $user->setVerified(false);
        $user->setRoles(['ROLE_ANONYMOUS']);
        $user->setDateRegister(new \DateTime('1970-01-01 00:00:00'));
    }
    public function anonymizeUser(User $user): void
    {
        // Il est recommandé d’encapsuler cette logique dans une transaction Doctrine pour garantir
        //  qu’aucune donnée n’est laissée dans un état incohérent si une erreur survient.
        $this->entityManager->beginTransaction();

        try {
            // Anonymisation des détails de l'utilisateur
            $this->anonymizeUserDetails($user);

            // Anonymisation des commandes et factures associées
            $this->processOrderAndInvoiceAnonymization($user);

            // Enregistrement et validation des changements
            $this->entityManager->flush();
            $this->entityManager->commit();

            $this->addFlash('success', 'Anonymisation terminée');
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new LogicException('Une erreur s’est produite lors de l\'anonymisation de l\'utilisateur.', 0, $e);
        }
    }


    // public function anonymizeAndDeleteUser(User $user): void
    // {

    //     // si une exception se produit, on  passe immédiatement au bloc catch.
    //     try {
    //         // Anonymisation les informations utilisateur
    //         $uniqueString = uniqid('anonymized_', true); // Préfixe 
    //         $user->setEmail($uniqueString . '@anonymized.com');
    //         $user->setPassword($this->passwordHasher->hashPassword($user, $uniqueString)); // Mot de passe hasher
    //         $user->setFirstName($uniqueString);
    //         $user->setLastName($uniqueString);
    //         $user->setPhoneNumber($uniqueString);
    //         $user->setUsername($uniqueString);
    //         $user->setPicture(null);
    //         $user->setCity($uniqueString);
    //         $user->setPortfolio($uniqueString);
    //         $user->setBio($uniqueString);
    //         $user->setVerified(false); // Si 'verified' est booléen
    //         $user->setRoles(['ROLE_ANONYMOUS']);
    //         $user->setDateRegister(new \DateTime('1970-01-01 00:00:00'));

    //          // on recherche les informations pertinentes a sauvegardés
    //          // avant
    //          $orders = $this->entityManager->getRepository(Order::class)->findBy(['user' => $user]);
    //          foreach ($orders as $order) {
    //              $order->setOrderTraceability([
    //                  'order_id' => $order->getId(),
    //                  'service_name' => $order->getService()->getName(),
    //                  'amount' => $order->getAmount(),
    //                  // autres informations pertinentes
    //              ]);
    //              $order->setInvoiceTraceability([
    //                  'invoice_id' => $order->getInvoice()->getId(),
    //                  'issue_date' => $order->getInvoice()->getIssueDate(),
    //                  'amount' => $order->getInvoice()->getAmount(),
    //                  // autres informations pertinentes
    //              ]);
    //              // Définir l'utilisateur sur `null`
    //              $order->setUserId(null);
    //          }
    //         // Enregistrement des modifications
    //         // enregistre toutes les modifications faites aux entités dans la base de données. 
    //         // Cela inclut les changements, ajouts, ou suppressions d’entités.
    //         // C’est à ce moment que Doctrine génère les requêtes SQL pour synchroniser l'état des objets avec la base de données.
    //         //$this->entityManager->flush();
    //         // commit : Validation de la transaction
    //         // indique à Doctrine que toutes les opérations de la transaction 
    //         // ont été réussies et qu’elles peuvent être validées de manière permanente dans la base de données.
    //         //$this->entityManager->commit();

    //         $this->addFlash('success', 'Anonymisation terminée');

    //     } catch (\Exception $e) {
    //         // En cas d'erreur, rollback des changements
    //         // annule toutes les modifications effectuées dans la transaction en cours.
    //         // Cela ramène la base de données à l'état précédent, avant le début de la transaction.
    //         $this->entityManager->rollback();
    //         // Après le rollback, une nouvelle exception LogicException est lancée pour signaler le problème, 
    //         // en ajoutant un message personnalisé et en passant l'exception d'origine pour conserver le contexte.
    //         throw new LogicException('Une erreur s’est produite lors de l\'anonymisation de l\'utilisateur.', 0, $e);
    //     }

    // }
}
