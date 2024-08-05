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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $serviceItemRepository;
    private $serializer;
    private $cart;
    private $logger;

    public function __construct(
        ServiceItemRepository $serviceItemRepository,
        SerializerInterface $serializer,
        Cart $cart,
        LoggerInterface $logger,
    ) {
        $this->serviceItemRepository = $serviceItemRepository;
        $this->serializer = $serializer;
        $this->cart = $cart;
        $this->logger = $logger;
    }

    #[Route('/cart', name: 'cart_product')]
    public function cartProduct(
        Request $request
    ): Response {
        $fullCart = $this->cart->getCart($request);

        $this->addFlash('positive', 'Votre commande sera ajoutée au panier');

        return $this->render('cart/index.html.twig', [
            'title_page' => 'Panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'nbProducts' => $fullCart['totalServiceItem'],
            'stripe_public_key' => $this->getParameter('stripe_public_key')
        ]);
    }

    #[Route('/cart/add/serviceItem/{id}', name: 'add_service_cart')]
    public function cartAddProduct(
        ServiceItem $serviceItem,
        Request $request,
    ): Response {

        if (!$serviceItem) {
            throw $this->createNotFoundException('Le service n\'existe pas');
        }
        // Ajouter le produit au panier
        $this->cart->addProduct($serviceItem, $request);

        // Obtenir le panier complet
        $fullCart = $this->cart->getCart($request);

        // Sérialiser les données du panier en JSON
        $jsonFullCart = $this->serializer->serialize($fullCart, 'json', ['groups' => 'cart']);
        // Log 
        $this->logger->info('Serialized cart data: ' . $jsonFullCart);
        $cookieName = 'cart';
        // créer ou mettre à jour le cookie
        $cookie = new Cookie(
            $cookieName,
            $jsonFullCart,
            time() + (30 * 24 * 60 * 60), // 30 jours
            '/', // Path
            null, // Domain, null pour utiliser le domaine actuel
            false, // Secure, true si vous utilisez HTTPS
            true, // HttpOnly
            false, // Raw
            'strict' // SameSite, peut être 'lax', 'strict', ou 'none'
        );
        // ajouter le cookie à la réponse
        $response = new Response();
        $response->headers->setCookie($cookie);
        // Log 
        $this->logger->info('Response headers before rendering view: ' . json_encode($response->headers->all()));

        // Ajouter le rendu de la vue 
        $content = $this->renderView('cart/index.html.twig', [
            'title_page' => 'Panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
        $response->setContent($content);
        // Log the final response headers
        $this->logger->info('Final response headers: ' . json_encode($response->headers->all()));
        return $response;
    }








    #[Route('/cart/totalItemFromCart', name: 'cart_total_item', methods: ['GET'])]
    public function getTotalItemFromCart(Cart $cart, Request $request): JsonResponse
    {
        $fullCart = $cart->getCart($request);

        try {
            // Retourner la réponse JSON 
            return new JsonResponse(['totalServiceItem' => $fullCart['totalServiceItem']], Response::HTTP_OK);
        } catch (\Throwable $e) {
            // Retourner une réponse JSON avec une erreur 500 en cas d'exception
            return new JsonResponse(['error' => 'Failed to array_sum serviceItem.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }








    #[Route('/cart/remove/{id}', name: 'remove_service_cart')]
    public function cartRemoveProduct(ServiceItem $serviceItem, Request $request, Cart $cart): Response
    {
        $cart->removeProduct($serviceItem, $request);
        $fullCart = $cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'Panier',
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
            'title_page' => 'Panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'service_pictures_directory' => $this->getParameter('service_pictures_directory')
        ]);
    }










    #[Route('/empty', name: 'empty')]
    public function empty(Cart $cart, Request $request)
    {
        $cart->empty($request);
        $fullCart = $cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'Panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total']
        ]);
    }
    // #[Route('/cart/create/order', name: 'add_order')]
    // public function createOrder(Cart $cart, Request $request, SerializerInterface $serializer): Response
    // {
    //     return $this->redirectToRoute('home');
    // }

}
