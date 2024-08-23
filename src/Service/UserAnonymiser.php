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
            $invoice = $order->getInvoice();

            if ($invoice) {
                $invoice->setOrderTraceability([
                    'order_id' => $order->getId(),
                    
                    'amount' => $order->getAmount(),
                ]);


                $order->setClientTraceability([
                    'invoice_id' => $invoice->getId(),
                    'user' => $order->getUser(),
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
}
