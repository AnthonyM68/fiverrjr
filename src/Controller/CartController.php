<?php

namespace App\Controller;

use App\Service\Cart;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Repository\ServiceItemRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cart;
    private $logger;

    public function __construct(Cart $cart, LoggerInterface $logger)
    {
        $this->cart = $cart;
        $this->logger = $logger;
    }

    #[Route('/cart', name: 'cart_product')]
    public function cartProduct(Request $request): Response
    {
        // on peu crée et vérifier ici un status de paiement
        // $status = $request->query->get('status', 'pending');

        $fullCart = $this->cart->getCart($request);
        $this->addFlash('positive', 'votre commande sera ajoutée au panier');

        return $this->render('cart/index.html.twig', [
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'nbProducts' => $fullCart['totalServiceItem'],
            'stripe_public_key' => $this->getParameter('stripe_public_key')
        ]);
    }

    #[Route('/cart/add/service/{id}', name: 'add_service_cart')]
    public function cartAddProduct(Cart $cart, Request $request, ServiceItem $serviceItem): Response
    {
        // Ajoute le produit au panier via le service Cart
        $cart->addProduct($serviceItem, $request);

        // Sérialise le panier mis à jour
        $serializedCart = $cart->serializeCart($request);
        $this->logger->info('Serialized cart data: ' . $serializedCart);

        // Crée un cookie avec le panier sérialisé
        $cookie = $cart->createCartCookie($serializedCart, $request);

        // Crée la réponse et ajoute le cookie à la réponse
        $response = new Response();
        $response->headers->setCookie($cookie);

        // Récupère le panier complet pour l'affichage
        $fullCart = $cart->getCart($request);

        $content = $this->renderView('cart/index.html.twig', [
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
        $response->setContent($content);

        return $response;
    }

    #[Route('/cart/totalItemFromCart', name: 'cart_total_item', methods: ['GET'])]
    public function getTotalItemFromCart(Cart $cart, Request $request): JsonResponse
    {
        try {
            $fullCart = $cart->getCart($request);
            return new JsonResponse(['totalServiceItem' => $fullCart['totalServiceItem']], Response::HTTP_OK);
        } catch (\Throwable $e) {
            $this->logger->error('error fetching total items from cart: ' . $e->getMessage());
            return new JsonResponse(['error' => 'failed to calculate total items in cart.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/cart/remove/{id}', name: 'remove_service_cart')]
    public function cartRemoveProduct(ServiceItem $serviceItem, Request $request, Cart $cart): Response
    {
        $cart->removeProduct($serviceItem, $request);
        $fullCart = $cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    #[Route('/cart/delete/{id}', name: 'delete_service_cart')]
    public function cartDeleteProduct(Cart $cart, ServiceItem $serviceItem, Request $request): Response
    {
        $cart->deleteProduct($serviceItem, $request);
        $fullCart = $cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'Panier en cours',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            // 'service_pictures_directory' => $this->getParameter('service_pictures_directory'),
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    #[Route('/empty', name: 'empty')]
    public function empty(Cart $cart, Request $request): Response
    {
        $cart->empty($request);
        $fullCart = $cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total']
        ]);
    }
}
