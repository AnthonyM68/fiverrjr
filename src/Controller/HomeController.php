<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Service;
use App\Entity\Category;
use App\Form\SearchFormType;
use App\Repository\UserRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $entityManager;
    // Constructeur pour injecter l'EntityManager
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * Route pour la page d'accueil
     *
     * @return Response
     */
    #[Route('/home', name: 'home')]
    public function index(Request $request): Response
    {

        return $this->render('home/index.html.twig', [
            'controller_name' => 'SearchController',
            // 'form_service' => $formTheme->createView(),
            // 'results' => $results,
            // 'search_term' => $searchTerm,
            'submitted_form' => null,
            'title_page' => 'Accueil'
        ]);
    }










    #[Route('/admin', name: 'admin')]
    public function administrator(): Response
    {
        return $this->render('administrator/index.html.twig', [
            'title_page' => 'Tableau de bord'
        ]);
    }

    
}
