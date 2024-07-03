<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Service;
use App\Entity\Category;
use App\Form\SearchFormType;
use Psr\Log\LoggerInterface;
use App\Form\ServiceSearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        // Création des trois instances de formulaire pour chaque type de recherche
        // $formTheme = $this->createForm(SearchFormType::class, null, [
        //     'search_table' => 'theme',
        //     'search_label' => 'Par Thême:',
        // ]);
        // $formCategory = $this->createForm(SearchFormType::class, null, [
        //     'search_table' => 'category',
        //     'search_label' => 'Par Catégorie:',
        // ]);
        $formCourse = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'course',
            'search_label' => 'Par Sous-Catégorie:',
        ]);
        // Gestion de la soumission des formulaires
        // $formTheme->handleRequest($request);
        // $formCategory->handleRequest($request);
        $formCourse->handleRequest($request);

        // Comptage des enregistrements
        // $themeCount = $this->entityManager->getRepository(Theme::class)->countAll();
        // $categoryCount = $this->entityManager->getRepository(Category::class)->countAll();
        $courseCount = $this->entityManager->getRepository(Course::class)->countAll();
        $serviceCount = $this->entityManager->getRepository(Service::class)->countAll();

        // Rendu de la vue avec les données des formulaires et les comptes d'enregistrements
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            // 'form_theme' => $formTheme->createView(),
            // 'form_category' => $formCategory->createView(),
            'form_course' => $formCourse->createView(),
            // 'theme_count' => $themeCount,
            // 'category_count' => $categoryCount,
            'course_count' => $courseCount,
            'service_count' => $serviceCount,
            'errors' => $formCourse->getErrors(true),
            'title_page' => 'Recherches avancées',
            'submitted_form' => null
        ]);
    }

    #[Route("/search/resultat", name: "search_resultat", methods: ["POST"])]
    public function searchResultat(Request $request): JsonResponse
    {
        $results = [];
        $submittedFormName = null;

        try {
            if ($request->isMethod('POST')) {
                // Vérification du formulaire soumis 
                if ($request->request->get('submitted_form_type') === 'service') {
                    // On récupère le term a rechercher
                    $searchTerm = $request->request->get('search_term');
                    // Récupération des résultats de recherche pour les services
                    $services = $this->entityManager->getRepository(Service::class)->findByTerm($searchTerm);
                    // Sérialisation des résultats de service
                    $results['service'] = array_map(function ($service) {
                        return [
                            'id' => $service->getId(),
                            'title' => $service->getTitle(),
                            'description' => $service->getDescription(),
                            'picture' => $service->getPicture(),
                        ];
                    }, $services);
                    $submittedFormName = 'service';
                } elseif ($request->request->get('submitted_form_type') === 'theme') {
                    // On récupère le term a rechercher
                    $searchTerm = $request->request->get('search_term');
                    // Récupération des résultats de recherche par theme->category->course => services 
                    $themes = $this->entityManager->getRepository(Theme::class)->findByTerm($searchTerm);
                    // Sérialisation des résultats de thème
                    $results['theme'] = array_map(function ($theme) {
                        return [
                            'id' => $theme->getId(),
                            'nameTheme' => $theme->getNameTheme(),
                        ];
                    }, $themes);
                    $submittedFormName = 'theme';
                }
                // Gestion des résultats vides
                if (empty($results[$submittedFormName])) {
                    $results['empty'] = true;
                }
                // Retour des données au format JSON
                return new JsonResponse([
                    'results' => $results,
                    'submitted_form' => $submittedFormName
                ]);
            }
            if (empty($results[$submittedFormName])) {
                $results['empty'] = true;
            }

            // On retourne les données au format JSON
            return new JsonResponse([
                'results' => $results,
                'submitted_form' => $submittedFormName
            ]);
        } catch (\Exception $e) {
            // Log the error message
            $this->logger->error('Error in searchResultat: ' . $e->getMessage());

            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
