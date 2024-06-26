<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Service;
use App\Entity\Category;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
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


    #[Route("/home/search/all", name: "all_search")]
    public function searchAll(Request $request): Response
    {
        $formTheme = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'theme',
            'search_label' => 'Recherchez votre service',
        ]);
        $formTheme->handleRequest($request);
        $results = [];
        $submittedFormName = null;
       // $searchTerm = null;

        if ($formTheme->isSubmitted() && $formTheme->isValid() && $request->request->get('submitted_form_type') === 'theme_category_course') {

            $searchTerm = $formTheme->get('search_term')->getData();
     
            $results = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
            if (empty($results)) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_service';
        }
        // dd($results);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'SearchController',
            'form_service' => $formTheme->createView(),
            'results' => $results,
            'search_term' => $searchTerm,
            'submitted_form' => $submittedFormName,
        ]);
    }

    #[Route("/home/service", name: "home_service_search")]
    public function search(Request $request): Response
    {
        $formService = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'service',
            'search_label' => 'Recherchez votre service',
        ]);
        $formService->handleRequest($request);
        $results = [];
        $submittedFormName = null;

        if ($formService->isSubmitted() && $formService->isValid() && $request->request->get('submitted_form_type') === 'service') {

            $searchTerm = $formService->get('search_term')->getData();
            
            $results['service'] = $this->entityManager->getRepository(Service::class)->findByTerm($searchTerm);
            
            if (empty($results['service'])) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_service';
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            'form_service' => $formService->createView(),
            'results' => $results,
            'submitted_form' => $submittedFormName,
        ]);
    }
}
