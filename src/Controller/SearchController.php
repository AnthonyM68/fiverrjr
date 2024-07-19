<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



// Controller de la recherche Avancée
class SearchController extends AbstractController
{
    private $entityManager;
    private $logger;
    private $csrfTokenManager;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        // SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->logger = $logger;
    }

    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        // Comptage des enregistrements
        // $themeCount = $this->entityManager->getRepository(Theme::class)->countAll();
        // $categoryCount = $this->entityManager->getRepository(Category::class)->countAll();
        // $courseCount = $this->entityManager->getRepository(Course::class)->countAll();
        $ServiceCount = $this->entityManager->getRepository(ServiceItem::class)->countAll();

        // $serviceItems = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds('Développement');
        // $results = $serviceItems->getQuery()->getResult();

        // Rendu de la vue avec les données des formulaires et les comptes d'enregistrements
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            // 'results' => $results,
            'service_count' => $ServiceCount,
            'search_term' => 'Développement'

        ]);
    }

    #[Route("/search/results", name: "search_results", methods: ['POST'])]
    public function searchResult(Request $request, SerializerInterface $serializer): JsonResponse
    {
        // Pour le test, on ne prend pas en compte le contenu du request et on récupère toutes les entités Theme
        // $queryBuilder = $this->entityManager->getRepository(ServiceItem::class)->findAll();

        $queryBuilder = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds('Developpement');

        // Sérialisation des entités Theme
        try {
            $results = $serializer->serialize($queryBuilder, JsonEncoder::FORMAT, ['groups' => 'serviceItem']);


            $this->logger->info('Serialized results:', ['results' => $results]);

            return new JsonResponse($results, 200, [], true);


        } catch (\Exception $e) {

            $this->logger->error('Serialization error:', ['exception' => $e]);

            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    // #[Route("/search/results", name: "search_results", methods: ['POST'])]
    // public function searchResult(Request $request, SerializerInterface $serializer): JsonResponse
    // {
    //  Récupération des donnéesdu formulaire au format JSON et les décodes
    // $jsonData = json_decode($request->getContent(), true);
    // s'il y' une valeur dans le champ du token on la sauvegarde
    // sinon token vaut null
    // $token = $jsonData['_token'] ?? null;
    // // si le token du formulaire personnalisé ViewSearch existe
    // if ($token) {
    //     // Si le token n'est pas présent
    //     if ($token === null) {
    //         return new JsonResponse([
    //             'error' => 'Token is not defined or null'
    //         ], JsonResponse::HTTP_BAD_REQUEST);
    //     }
    //     // Si le token n'est pas valide
    //     if (!$this->isCsrfTokenValid('search_item', $token)) {
    //         return new JsonResponse([
    //             'error' => 'Invalid CSRF token!'
    //         ], JsonResponse::HTTP_BAD_REQUEST);
    //     }
    // }
    // // on récupère la valeur des champs
    // $searchTerm = $jsonData['search_form[search_term]'] ?? null;
    // $this->logger->info('Term search:', ['search_form[search_term]' => $searchTerm]);

    // $submittedFormType = $jsonData['submitted_form_type'] ?? null;
    // $this->logger->info('Form submitted:', ['submitted_form_type' => $submittedFormType]);

    // $priceFilter = $jsonData['price_filter'] ?? null;
    // $this->logger->info('Price:', ['priceFilter' => $priceFilter]);

    // Récupération des résultats de recherche pour les ServiceItems
    // $queryBuilder = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
    // $queryBuilder = $this->entityManager->getRepository(Theme::class)->findAll();
    // on récupère la valeur du filtre
    // $priceFilter = $jsonData['price_filter'] ?? null;
    // // on trie les résultats
    // if ($priceFilter === 'low_to_high') {
    //     $queryBuilder->orderBy('si.price', 'ASC');
    // } elseif ($priceFilter === 'high_to_low') {
    //     $queryBuilder->orderBy('si.price', 'DESC');
    // }
    // on recherche le resultat
    // $serviceItems = $queryBuilder->getQuery()->getResult();
    // On sérialise un tableau d'objet complexe
    // on traite également les références circulaire et attribuons
    // un ID unique pour éviter les boucles infinie 
    // try {

    // $results = $serializer->serialize($queryBuilder, JsonEncoder::FORMAT);

    //     $this->logger->info('Serialized results:', ['results' => $results]);

    //     return new JsonResponse($results, 200, [], true);
    // } catch (\Exception $e) {

    //     $this->logger->error('Serialization error:', ['exception' => $e]);

    //     return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

    // }
    // return new JsonResponse($queryBuilder, 200, [], true);
    // }
}
