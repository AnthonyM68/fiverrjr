<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Service\CartService;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarController extends AbstractController
{
    #[Route('/navbar', name: 'app_navbar')]
    public function index(
        Request $request,
        CartService $cart

    ): Response {
        // récupère le panier depuis le service
         $cart = $cart->getCart($request);

        $formServiceDesktop = $this->createForm(SearchFormType::class, null, [
            'id_suffix' => 'desktop', // Identifiant unique pour la version desktop

        ]);
        $formServiceMobile = $this->createForm(SearchFormType::class, null, [
            'id_suffix' => 'mobile', // Identifiant unique pour la version mobile

        ]);
        return $this->render('navbar/index.html.twig', [
            'formServiceDesktop' => $formServiceDesktop->createView(),
            'formServiceMobile' => $formServiceMobile->createView(),
            'page' => '1',
            'totalServiceItem' => '',
            'total' => '',
        ]);
    }
}
