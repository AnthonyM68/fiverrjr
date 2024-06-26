<?php

namespace App\Controller;

use App\Form\SearchFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'title_page' => 'Accueil'
        ]);
    }

    #[Route('/admin', name: 'admin')]
    public function administrator(): Response
    {
        return $this->render('administrator/index.html.twig', [
            'controller_name' => 'HomeController',
            'title_page' => 'Tableau de bord'
        ]);
    }
}
