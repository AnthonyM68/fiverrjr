<?php

namespace App\Service;

use LogicException;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAnonymizer extends AbstractController
{
    private $logger;
    private $serializer;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }
    private function processServiceDelete(User $user): void
    {
        // Récupère tous les services associés à l'utilisateur
        $services = $this->entityManager->getRepository(ServiceItem::class)->findBy(['user' => $user]);
        foreach ($services as $service) {
            // Suppression du service
            $this->entityManager->remove($service);
        }
        // Assurez-vous de persister les modifications après avoir supprimé les services
        $this->entityManager->flush();
    }

    private function processOrderAndInvoiceAnonymization(User $user): void
    {
        // on recherche toutes les commandes de l'utilisateur
        $orders = $this->entityManager->getRepository(Order::class)->findBy(['user' => $user]);
        // on itère sur chaque commande
        foreach ($orders as $order) {

            // on s'assure que si une commande existe mais n'a ni paiement ni facture associée
            // on la supprime
            if ($order->getPayment() === null && $order->getInvoice() === null) {
                $this->entityManager->remove($order);
                // on passe à la commande suivante
                continue;
            }
            // on recherche chaque facure
            $invoice = $order->getInvoice();
            $this->logger->info('Invoice: ', ['invoice' => $invoice,]);
            // on recherche et serialize les informations de paiement
            $payment = $this->serializer->serialize($order->getPayment(), 'json', ['groups' => 'payment']);
            $paymentSerialize = json_decode($payment, true);
            $this->logger->info('paymentSerialize: ', ['paymentSerialize' => $paymentSerialize]);
            /**
             * on dissocie l'utilisateur de la commande
             */
            $order->setUser(null);

            // on serialize les information de l acommande
            $order = $this->serializer->serialize($order, 'json', ['groups' => 'order']);
            $orderSerialize = json_decode($order, true);
            $this->logger->info('orderSerialize: ', ['orderSerialize' => $orderSerialize]);

            // si la facture existe, on ajoute les informations de traçabilité
            if ($invoice) {
                // on serialise les informations de la commande dans la facture
                $orderTraceability = [
                    'order' => $orderSerialize,
                    'payment' => $paymentSerialize, // on ajoute les informations de paiement serialiser en clair
                ];

                // on convertit le tableau en JSON et l'ajoutons au champ order_traceability de invoice
                $invoice->setOrderTraceability(json_encode($orderTraceability));
                // on sérialise les informations minmial du client  
                $user = $this->serializer->serialize($user, "json", ['groups' => 'userTraceability']);
                $userSerializer = json_decode($user, true);
                $this->logger->info('userSerializer: ', ['userSerializer' => $userSerializer]);
                // on serialise les informations de traçabilité du client dans la facture
                $clientTraceability = [
                    'invoice_id' => $invoice->getId(),
                    'user' => $userSerializer, // on ajoute les informations du client
                ];
                $this->logger->info('clientTraceability', ['client' => $clientTraceability]);
                // on convertit le tableau en JSON et l'ajoutons au champ client_traceability de invoice
                $invoice->setClientTraceability(json_encode($clientTraceability));
            }
            /**
             * on persiste les informations dans la facture
             */
            if ($invoice) {
                $this->entityManager->persist($invoice);
            }
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

        $this->entityManager->persist($user);
    }
    public function anonymizeUser(User $user): void
    {
        // Il est recommandé d’encapsuler cette logique dans une transaction Doctrine pour garantir
        //  qu’aucune donnée n’est laissée dans un état incohérent si une erreur survient.
        $this->entityManager->beginTransaction();

        try {
            // Suppression des services associés à l'utilisateur
            $this->processServiceDelete($user);
            // Anonymisation des commandes 
            $this->processOrderAndInvoiceAnonymization($user);
            // anonymisation l'utilisateur
            $this->anonymizeUserDetails($user);
            // Enregistrer toutes les modifications dans la base de données
            $this->entityManager->flush();
            $this->entityManager->commit();

            $this->addFlash('success', 'Anonymisation terminée');
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new LogicException('Une erreur s’est produite lors de l\'anonymisation de l\'utilisateur.', 0, $e);
        }
    }
}
