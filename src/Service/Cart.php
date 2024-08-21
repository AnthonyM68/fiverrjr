<?php

namespace App\Service;

use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use App\Repository\ServiceItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Cart
{
    private $entityManager;
    private $serviceItemRepository;
    private $imageService;
    private $httpKernel;
    private $logger;
    private $serializer;

    public function __construct(
        ServiceItemRepository $serviceItemRepository,
        EntityManagerInterface $entityManager,
        HttpKernelInterface $httpKernel,
        LoggerInterface $logger,
        ImageService $imageService,
        SerializerInterface $serializer
    ) {
        // injecte les dépendances nécessaires pour manipuler les services et gérer les données du panier
        $this->entityManager = $entityManager;
        $this->serviceItemRepository = $serviceItemRepository;
        $this->httpKernel = $httpKernel;
        $this->logger = $logger;
        $this->imageService = $imageService;
        $this->serializer = $serializer;

    }

    // récupère les données du panier depuis la session
    public function getCart(Request $request)
    {
        // récupère la session
        $session = $request->getSession();
        // on récupère le panier s'il existe ou initialise un tableau vide
        $cart = $session->get('cart', []);

        // initialise les variables pour contenir les données du panier
        $data = [];
        $total = 0;
        $totalServiceItem = 0;

        // on itère sur chaque élément du panier pour obtenir les détails des services ajoutés
        foreach ($cart as $id => $quantity) {
            $service = $this->serviceItemRepository->find($id);

            if ($service) {
              

                // récupère et génère l'url de l'image pour chaque service
                $originalFilename = $service->getPicture();
                $this->logger->info('processing generateImageUrl service cart', ['originalFilename' => $originalFilename]);

                if ($originalFilename) {
                    try {
                        // on extrait seulement le nom du fichier et l'extension
                        $filename = basename($originalFilename);  
        
                        // génère l'url de l'image et la met à jour dans l'entité
                        $pictureUrl = $this->imageService->generateImageUrl($filename, 'SERVICE');
                        $service->setPicture($pictureUrl);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                }

                // ajoute les détails du service et la quantité à l'ensemble des données du panier
                $data[] = [
                    'serviceItem' => [
                        'id' => $service->getId(),
                        'title' => $service->getTitle(),
                        'price' => $service->getPrice(),
                        'picture' => $service->getPicture()
                    ],
                    'quantity' => $quantity
                ];

                // calcule le total et le nombre d'articles dans le panier
                $total += $service->getPrice() * $quantity;
                $totalServiceItem += $quantity;
            }
        }
        // retourne un tableau contenant les données du panier, le total et le nombre total d'articles
        return [
            'data' => $data,
            'total' => $total,
            'totalServiceItem' => $totalServiceItem
        ];
    }

    // ajoute un produit au panier ou augmente sa quantité si déjà présent
    public function addProduct(ServiceItem $serviceItem, Request $request)
    {
        // récupère la session et le panier actuel
        $session = $request->getSession();
        $id = $serviceItem->getId();
        $cart = $session->get('cart', []);

        // si le produit est déjà dans le panier, augmente sa quantité
        if (isset($cart[$id])) {
            $cart[$id]++;
        } else {
            // sinon, ajoute le produit avec une quantité de 1
            $cart[$id] = 1;
        }

        // met à jour le panier dans la session
        $session->set('cart', $cart);
    }

    // retire une unité d'un produit ou le supprime si la quantité est de 1
    public function removeProduct(ServiceItem $serviceItem, Request $request)
    {
        // récupère la session et le panier actuel
        $session = $request->getSession();
        $id = $serviceItem->getId();
        $cart = $session->get('cart', []);

        // vérifie si le produit est dans le panier
        if (isset($cart[$id])) {
            // si la quantité est supérieure à 1, la diminue
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                // sinon, supprime le produit du panier
                unset($cart[$id]);
            }
        }

        // met à jour le panier dans la session
        $session->set('cart', $cart);
    }

    // supprime complètement un produit du panier
    public function deleteProduct(ServiceItem $serviceItem, Request $request)
    {
        // récupère la session et le panier actuel
        $session = $request->getSession();
        $id = $serviceItem->getId();
        $cart = $session->get('cart', []);

        // vérifie si le produit est dans le panier et le supprime
        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        // met à jour le panier dans la session
        $session->set('cart', $cart);
    }

    // vide complètement le panier
    public function empty(Request $request)
    {
        // récupère la session et supprime les données du panier
        $session = $request->getSession();
        $session->remove('cart');
    }

    public function serializeCart(Request $request): string
    {

        $fullCart = $this->getCart($request);
        return $this->serializer->serialize($fullCart, 'json', ['groups' => 'cart']);
    }
    public function createCartCookie(string $serializedCart, Request $request): Cookie
    {
        return new Cookie(
            'cart',
            $serializedCart,
            time() + (30 * 24 * 60 * 60), // 30 jours
            '/',
            null,
            $request->isSecure(), // définit 'secure' basé sur la connexion HTTPS
            true, // HttpOnly
            false,
            'strict'
        );
    }

}
