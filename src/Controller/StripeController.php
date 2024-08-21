<?php
// src/Controller/StripeController.php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Order;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(Cart $cart, Request $request): Response
    {
        $fullCart = $cart->getCart($request);
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
    public function createCharge(Cart $cart, EntityManagerInterface $entityManager, Request $request): Response
    {

        if ($request->isMethod('POST')) {
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

                // on rée la commande
                $commande = new Order();
                // on associe l'utilisateur connecté
                $commande->setUser($this->getUser());
                // euros
                $commande->setAmount($amount / 100);
                
                $entityManager->persist($commande);
                $entityManager->flush();

                // Vider le panier après le paiement
                $cart->empty($request);

                $this->addFlash('success', 'Payment Successful!');

                return $this->redirectToRoute('cart_product', ['status' => 'paid'], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
            }
        }

        // Si la requête n'est pas POST ou le token n'est pas présent, rediriger ou afficher une erreur
        return $this->redirectToRoute('app_stripe');
    }
}
