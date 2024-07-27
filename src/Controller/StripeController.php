<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'service_pictures_directory' => $this->getParameter('service_pictures_directory')
        ]);
    }


    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request)
    {
        if ($request->isMethod('POST')) {
            \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));
            Charge::create([
                "amount" => 5 * 100,
                "currency" => "eur",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
            ]);
        }
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}
