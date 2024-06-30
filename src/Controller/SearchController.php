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
        $formTheme = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'theme',
            'search_label' => 'Par Thême:',
        ]);
        $formCategory = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'category',
            'search_label' => 'Par Catégorie:',
        ]);
        $formCourse = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'course',
            'search_label' => 'Par Sous-Catégorie:',
        ]);
        // Gestion de la soumission des formulaires
        $formTheme->handleRequest($request);
        $formCategory->handleRequest($request);
        $formCourse->handleRequest($request);

        // Comptage des enregistrements
        $themeCount = $this->entityManager->getRepository(Theme::class)->countAll();
        $categoryCount = $this->entityManager->getRepository(Category::class)->countAll();
        $courseCount = $this->entityManager->getRepository(Course::class)->countAll();
        $serviceCount = $this->entityManager->getRepository(Service::class)->countAll();

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            'form_theme' => $formTheme->createView(),
            'form_category' => $formCategory->createView(),
            'form_course' => $formCourse->createView(),
            'theme_count' => $themeCount,
            'category_count' => $categoryCount,
            'course_count' => $courseCount,
            'service_count' => $serviceCount,
            'errors' => $formTheme->getErrors(true), // Ajoutez les erreurs ici
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
                if ($request->request->get('submitted_form_type') === 'theme') {
                    $searchTerm = $request->request->get('search_term');
                    $results['theme'] = $this->entityManager->getRepository(Theme::class)->findByTerm($searchTerm);
                    $submittedFormName = 'form_theme';
                } elseif ($request->request->get('submitted_form_type') === 'service') {
                    $searchTerm = $request->request->get('search_term');
                    $results['service'] = $this->entityManager->getRepository(Service::class)->findByTerm($searchTerm);
                    $submittedFormName = 'form_service';
                }
                // } elseif ($request->request->get('submitted_form_type') === 'category') {
                //     $searchTerm = $request->request->get('search_term');
                //     $results['category'] = $this->entityManager->getRepository(Category::class)->findByTerm($searchTerm);
                //     $submittedFormName = 'form_category';
                // } elseif ($request->request->get('submitted_form_type') === 'course') {
                //     $searchTerm = $request->request->get('search_term');
                //     $results['course'] = $this->entityManager->getRepository(Course::class)->findByTerm($searchTerm);
                //     $submittedFormName = 'form_course';
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
