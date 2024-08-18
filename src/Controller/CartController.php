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
    public function cartProduct(Request $request): Response
    {
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
    public function cartAddProduct(ServiceItem $serviceItem, Request $request): Response
    {
        if (!$serviceItem) {
            throw $this->createNotFoundException('le service n\'existe pas');
        }

        $this->cart->addProduct($serviceItem, $request);
        $fullCart = $this->cart->getCart($request);

        $jsonFullCart = $this->serializer->serialize($fullCart, 'json', ['groups' => 'cart']);
        $this->logger->info('serialized cart data: ' . $jsonFullCart);

        $cookie = new Cookie(
            'cart',
            $jsonFullCart,
            time() + (30 * 24 * 60 * 60), // 30 jours
            '/',
            null,
            $request->isSecure(), // définit 'secure' basé sur la connexion HTTPS
            true, // HttpOnly
            false,
            'strict'
        );

        $response = new Response();
        $response->headers->setCookie($cookie);

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
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'service_pictures_directory' => $this->getParameter('service_pictures_directory')
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

    // #[Route('/cart/create/order', name: 'add_order')]
    // public function createOrder(Cart $cart, Request $request, SerializerInterface $serializer): Response
    // {
    //     return $this->redirectToRoute('home');
    // }

}

