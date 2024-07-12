<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\Theme;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Controller de la recherche Avancée
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
     // Enregistrement des données de la requête dans les logs
    //  $this->logger->info('Received searchResultat form data', []);

    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        // Comptage des enregistrements
        // $themeCount = $this->entityManager->getRepository(Theme::class)->countAll();
        // $categoryCount = $this->entityManager->getRepository(Category::class)->countAll();
        // $courseCount = $this->entityManager->getRepository(Course::class)->countAll();
        $ServiceCount = $this->entityManager->getRepository(ServiceItem::class)->countAll();

        // Rendu de la vue avec les données des formulaires et les comptes d'enregistrements
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            // 'form' => $formSearch->createView(),
            // 'course_count' => $courseCount,
            'service_count' => $ServiceCount,

        ]);
    }
    // Réponse requête AJAX (ViewNavabar.js)
    #[Route("/search/results", name: "search_results", methods: ["POST"])]
    // public function searchResultat(Request $request): JsonResponse
    public function searchResultat(Request $request): JsonResponse
    {
        // Récupération des donnéesdu formulaire au format JSON et les décodes
        $jsonData = json_decode($request->getContent(), true);

        // s'il y' une valeur dans le champ du token on la sauvegarde
        // sinon token vaut null
        $token = $jsonData['_token'] ?? null;
        // si le token du formulaire personnalisé ViewSearch existe
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
        $this->logger->info('Term search:', ['search_term' => $searchTerm]);

        $submittedFormType = $jsonData['submitted_form_type'] ?? null;
        $this->logger->info('Form submitted:', ['submitted_form_type' => $submittedFormType]);

        $priceFilter = $jsonData['price_filter'] ?? null;
        $this->logger->info('Price:', ['priceFilter' => $priceFilter]);

        // Récupération des résultats de recherche pour les ServiceItems
        $results = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);

        // $ServiceItems = $queryBuilder->getQuery()->getResult();
       // Log des résultats pour vérification
       $this->logger->info('Search results', ['results' => $results]);

        // On filtre par prix
        // if ($priceFilter === 'low_to_high') {
        //     $results->orderBy('s.price', 'ASC');
        // } elseif ($priceFilter === 'high_to_low') {
        //     $results->orderBy('s.price', 'DESC');
        // }
        // Retourner les résultats au format JSON*/
        return new JsonResponse($queryBuilder);
                           
    }
}
