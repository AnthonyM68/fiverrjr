<?php
// src/Controller/StripeController.php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request): Response
    {

        if ($request->isMethod('POST')) {
            $stripeToken = $request->request->get('stripeToken');
            $amount = $request->request->get('amount', 500);

            try {
                Stripe::setApiKey($this->getParameter('stripe_secret_key'));

                Charge::create([
                    'amount' => $amount,
                    'currency' => 'eur',
                    'source' => $stripeToken,
                    'description' => 'Achat en ligne',
                ]);

                // Créer la commande
                $commande = new Order();
                $commande->setUser($this->getUser()); // Associer l'utilisateur connecté
                $commande->setTotal($amount / 100); // En euros

                $this->addFlash('success', 'Payment Successful!');

                return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
            }
        }

        // Si la requête n'est pas POST ou le token n'est pas présent, rediriger ou afficher une erreur
        return $this->redirectToRoute('app_stripe');
    }
}
