<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Service;
use App\Entity\Category;
use App\Form\SearchFormType;
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
        // Création et gestion du formulaire de recherche
        $formTheme = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'theme',
            'search_label' => 'Recherchez votre service',
        ]);
        $formTheme->handleRequest($request);
        $results = [];
        $searchTerm = null;
        $submittedFormName = null;
        // Vérification si le formulaire est soumis et valide
        if ($formTheme->isSubmitted() && $formTheme->isValid() && $request->request->get('submitted_form_type') === 'theme_category_course') {
            // On récupére les données de l'input
            $searchTerm = $formTheme->get('search_term')->getData();
            // Recherche des résultats correspondants au terme de recherche
            $results = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
            // Si aucun résultat n'est trouvé, on ajoute un indicateur 'empty'
            if (empty($results)) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_service';
        }
        // Rendu de la vue avec les résultats de la recherche
        return $this->render('home/index.html.twig', [
            'controller_name' => 'SearchController',
            'form_service' => $formTheme->createView(),
            'results' => $results,
            'search_term' => $searchTerm,
            'submitted_form' => $submittedFormName,
            'title_page' => 'Accueil'
        ]);
    }
    /* Détails d'un service*/
    #[Route('/home/service/{id}/detail', name: 'detail_service_home')]
    public function detailService(Service $service, Request $request, ServiceRepository $serviceRepository): Response
    {
        $service = $serviceRepository->find(["id" => $service->getId()]);
        // dd($service);
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
            'view_name' => 'formation/detail.html.twig',
            "service" => $service
        ]);
    }
    /**
     * Page d'administration
     *
     * @return Response
     */
    #[Route('/admin', name: 'admin')]
    public function administrator(): Response
    {
        return $this->render('administrator/index.html.twig', [
            'controller_name' => 'HomeController',
            'title_page' => 'Tableau de bord'
        ]);
    }


    /**
     * Route pour la recherche dans l'entité Service
     *
     * @param Request $request
     * @return Response
     */
    #[Route("/home/service", name: "home_service_search")]
    public function search(Request $request): Response
    {
        // Création et gestion du formulaire de recherche
        $formService = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'service',
            'search_label' => 'Recherchez votre service',
        ]);
        $formService->handleRequest($request);
        $results = [];
        $submittedFormName = null;
        // Vérification si le formulaire est soumis et valide
        if ($formService->isSubmitted() && $formService->isValid() && $request->request->get('submitted_form_type') === 'service') {
            // On récupére les données de l'input
            $searchTerm = $formService->get('search_term')->getData();
            // Recherche des résultats correspondants au terme de recherche dans l'entité Service
            $results['service'] = $this->entityManager->getRepository(Service::class)->findByTerm($searchTerm);
            // Si aucun résultat n'est trouvé, on ajoute un indicateur 'empty'
            if (empty($results['service'])) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_service';
        }
        // Rendu de la vue avec les résultats de la recherche
        return $this->render('home/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            'form_service' => $formService->createView(),
            'results' => $results,
            'submitted_form' => $submittedFormName,
        ]);
    }
}
