<?php
// src/Controller/StripeController.php

namespace App\Controller;


use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Order;
use Psr\Log\LoggerInterface;
use App\Service\CartService;
use App\Service\InvoiceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class StripeController extends AbstractController
{
    private $cart;
    private $invoiceService;
    private $entityManager;
    private $logger;

    public function __construct(CartService $cart, EntityManagerInterface $entityManager, InvoiceService $invoiceService, LoggerInterface $logger)
    {
        $this->cart = $cart;
        $this->invoiceService = $invoiceService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    #[Route('/stripe', name: 'app_stripe')]
    public function index(Request $request): Response
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


    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request): Response
    {

        $this->logger->info('charge', ['charge' =>  $request]);
        if ($request->isMethod('POST')) {
            $this->logger->info('POST', ['POST' =>  $request]);
            
            $stripeToken = $request->request->get('stripeToken');
            $amount = $request->request->get('amount', 0);

            try {
                Stripe::setApiKey($this->getParameter('stripe_secret_key'));

                Charge::create([
                    'amount' => $amount,
                    'currency' => 'eur',
                    'source' => $stripeToken,
                    'description' => 'Achat en ligne',
                ]);
                $order = $this->cart->createOrder($request);
                // on enregistre les informations de la commande en base de données
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                // on crée un paiement
                $payment = $this->cart->createPayment($request, $order);

                // on génére le PDF
                $pdfPath = $this->invoiceService->generateInvoicePdf([
                    'order' => $order,
                    'payment' => $payment,
                ]);
                // on enregistre les informations du paiement en base de données
                $this->entityManager->persist($payment);
                $this->entityManager->flush();

                // on génére la facture au format pdf
                $invoice = $this->invoiceService->createInvoice($order, $pdfPath);

                // on enregistre les informations de la facture en base de données
                $this->entityManager->persist($invoice);
                $this->entityManager->flush();
                // Vider le panier
                $this->cart->empty($request);

                $this->addFlash('success', 'Paiement validé');

                return $this->redirectToRoute('cart_product', ['status' => 'paid'], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                //$this->addFlash('error', $e->getMessage());
                $this->logger->info('error', ['error' =>   $e->getMessage()]);
                return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->redirectToRoute('app_stripe');
    }
}
