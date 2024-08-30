<?php

namespace App\Controller;

use App\Entity\ServiceItem;
use App\Service\CartService;
use Psr\Log\LoggerInterface;
use App\Service\InvoiceService;
use App\Repository\OrderRepository;
use App\Repository\ServiceItemRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CartController extends AbstractController
{
    private $cart;
    private $logger;
    private $invoiceService;


    public function __construct(
        CartService $cart, 
        LoggerInterface $logger,
        InvoiceService  $invoiceService,
        
        )
    {
        $this->cart = $cart;
        $this->logger = $logger;
        $this->invoiceService = $invoiceService;
        
        
    }
    // affiche le panier
    #[Route('/client/cart', name: 'cart_product')]
    #[IsGranted('ROLE_CLIENT')]
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
    // ajoute un service au panier
    #[Route('/client/cart/add/service/{id}', name: 'add_service_cart')]
    #[IsGranted('ROLE_CLIENT')]
    public function cartAddProduct(Request $request, ServiceItem $serviceItem): Response
    {
        // on joute le produit au panier via le service Cart
        $this->cart->addProduct($serviceItem, $request);

        // on sérialise le panier mis à jour
        $serializedCart = $this->cart->serializeCart($request);
        $this->logger->info('Serialized cart data: ' . $serializedCart);

        // on crée un cookie avec le panier sérialisé
        $cookie = $this->cart->createCartCookie($serializedCart, $request);

        // Crée la réponse et ajoute le cookie à la réponse
        $response = new Response();
        $response->headers->setCookie($cookie);

        // Récupère le panier complet pour l'affichage
        $fullCart = $this->cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }


    #[Route('/client/cart/totalItemFromCart', name: 'cart_total_item', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function getTotalItemFromCart(Request $request): JsonResponse
    {
        try {
            $fullCart = $this->cart->getCart($request);
            return new JsonResponse(['totalServiceItem' => $fullCart['totalServiceItem']], Response::HTTP_OK);
        } catch (\Throwable $e) {
            $this->logger->error('error fetching total items from cart: ' . $e->getMessage());
            return new JsonResponse(['error' => 'failed to calculate total items in cart.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/client/cart/remove/{id}', name: 'remove_service_cart')]
    #[IsGranted('ROLE_CLIENT')]
    public function cartRemoveProduct(ServiceItem $serviceItem, Request $request): Response
    {
        $this->cart->removeProduct($serviceItem, $request);
        $fullCart = $this->cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }
    // supprime un service par ID du panier
    #[Route('/client/cart/delete/{id}', name: 'delete_service_cart')]
    #[IsGranted('ROLE_CLIENT')]
    public function cartDeleteProduct(ServiceItem $serviceItem, Request $request): Response
    {
        $this->cart->deleteProduct($serviceItem, $request);
        $fullCart = $this->cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'Panier en cours',
            'data' => $fullCart['data'],
            'total' => $fullCart['total'],
            // 'service_pictures_directory' => $this->getParameter('service_pictures_directory'),
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    #[Route('/client/empty', name: 'empty')]
    #[IsGranted('ROLE_CLIENT')]
    public function empty(Request $request): Response
    {
        $this->cart->empty($request);
        $fullCart = $this->cart->getCart($request);

        return $this->render('cart/index.html.twig', [
            'title_page' => 'panier',
            'data' => $fullCart['data'],
            'total' => $fullCart['total']
        ]);
    }

    #[Route('/client/invoice/pdf/{id}', name: 'invoice_pdf')]
    #[IsGranted('ROLE_CLIENT')]
    public function downloadPdf($id, Request $request): Response
    {
        return $this->invoiceService->downloadPdf($id, $request);
    }

    #[Route('/client/invoice/delete/completed/{id}', name: 'delete_completed_invoice')]
    #[IsGranted('ROLE_CLIENT')]
    public function deleteCompletedInvoice(int $id): RedirectResponse
    {
        try {
            // appel au service pour supprimer la facture
            $this->invoiceService->deleteCompletedInvoice($id);
            $this->addFlash('success', 'La facture a été supprimée avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression de la facture : ' . $e->getMessage());
        }

        return $this->redirectToRoute('cart_completed_orders');
    }
}
