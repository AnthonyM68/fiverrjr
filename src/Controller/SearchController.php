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
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private $entityManager;
    private $logger;
    private $csrfTokenManager;

    public function __construct(EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->logger = $logger;
    }


    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        $formSearch = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'course',
            'search_label' => 'Par Sous-Catégorie:'
        ]);
        // Gestion de la soumission des formulaires
        $formSearch->handleRequest($request);

        // Comptage des enregistrements
        // $themeCount = $this->entityManager->getRepository(Theme::class)->countAll();
        // $categoryCount = $this->entityManager->getRepository(Category::class)->countAll();
        // $courseCount = $this->entityManager->getRepository(Course::class)->countAll();
        $serviceCount = $this->entityManager->getRepository(Service::class)->countAll();

        // Rendu de la vue avec les données des formulaires et les comptes d'enregistrements
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            // 'form' => $formCourse->createView(),
            // 'course_count' => $courseCount,
            'service_count' => $serviceCount,
            'errors' => $formSearch->getErrors(true),
            'submitted_form' => null
        ]);
    }

    #[Route("/search/results", name: "search_results", methods: ["POST"])]
    public function searchResultat(Request $request): JsonResponse
    {
        // Récupération des données JSON
        $jsonData = json_decode($request->getContent(), true);
        
        // Récupération des champs nécessaires
        // Si le token du formulaire personnalisé ViewSearch existe
        $token = $jsonData['_token'] ?? null;
        if ($token) {
            // Si le token n'est pas présent
            if ($token === null) {
                return new JsonResponse([
                    'error' => 'Token is not defined or null'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
            // Si le token n'est pas valide
            if (!$this->isCsrfTokenValid('search_item', $token)) {
                return new JsonResponse([
                    'error' => 'Invalid CSRF token!'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }
        $searchTerm = $jsonData['search_term'] ?? null;
        $submittedFormType = $jsonData['submitted_form_type'] ?? null;
        $priceFilter = $jsonData['price_filter'] ?? null;

        // Enregistrement des données de la requête dans les logs
        $this->logger->info('Received searchResultat form data', [
            'token' => $token,
            'searchTerm' => $searchTerm,
            'submittedFormType' => $submittedFormType,
            'priceFilter' => $priceFilter
        ]);

        // Récupération des résultats de recherche pour les services
        $queryBuilder = $this->entityManager->getRepository(Service::class)->findByTerm($searchTerm);
        // On filtre par prix
        if ($priceFilter === 'low_to_high') {
            $queryBuilder->orderBy('s.price', 'ASC');
        } elseif ($priceFilter === 'high_to_low') {
            $queryBuilder->orderBy('s.price', 'DESC');
        }

        $services = $queryBuilder->getQuery()->getResult();

        // Sérialisation des résultats de service
        $results['service'] = array_map(function ($service) {
            return [
                'id' => $service->getId(),
                'title' => $service->getTitle(),
                'description' => $service->getDescription(),
                'picture' => $service->getPicture(),
                'price' => $service->getPrice(),
            ];
        }, $services);

        $submittedFormName = 'service';
        $results['submitted_form'] = $submittedFormName;

        // Retourner les résultats au format JSON
        return new JsonResponse($results);
    }
}
