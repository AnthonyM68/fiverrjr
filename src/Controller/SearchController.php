<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Theme;
use PHPUnit\Util\Json;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



// Controller de la recherche Avancée
class SearchController extends AbstractController
{
    private $entityManager;
    private $imageService;
    private $logger;
    private $csrfTokenManager;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        ImageService $imageService,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->imageService = $imageService;
        $this->serializer = $serializer;
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
            $results = $serializer->serialize($queryBuilder->getQuery()->getResult(), JsonEncoder::FORMAT, ['groups' => 'serviceItem']);
            $this->logger->info('Serialized results:', ['results' => $results]);
            return new JsonResponse($results, 200, [], true);
        } catch (\Exception $e) {

            $this->logger->error('Serialization error:', ['exception' => $e]);
            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route("/search/developer/name", name: "search_developer", methods: ['POST'])]
    public function searchDeveloper(Request $request): JsonResponse
    {
        // Récupération des données du formulaire
        $formData = $request->request->all();

        $searchTerm = $formData['search-user-by-name'];
        $token = $formData['_token'];
        // Vérifier le token CSRF
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('search_item_user', $token))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
        }
        $users = $this->entityManager->getRepository(User::class)->searchByTerm($searchTerm);

        foreach($users as $user) {
            $this->imageService->setPictureUrl($user);
        }
        // formater la date
        $usersFormatDate = array_map(function ($user) {
            return [
                'user' => $user,
                'formattedDate' => $user->getDateRegister()->format('d/m/Y'),
                'profileUrl' =>  $this->generateUrl('detail_user', ['id' => $user->getId()]),
                'listServices' => $this->generateUrl('list_services_by_userID', ['id' => $user->getId()])
            ];
        }, $users);
        try {
            $results = $this->serializer->serialize($usersFormatDate, JsonEncoder::FORMAT, ['groups' => 'user']);
            $this->logger->info('Serialized results:', ['results' => $results]);
            return new JsonResponse($results, 200, [], true);
        } catch (\Exception $e) {
            $this->logger->error('Serialization error:', ['exception' => $e]);
            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /*#[Route("/search/results", name: "search_results", methods: ['POST'])]
    public function searchResult(Request $request, SerializerInterface $serializer): JsonResponse
    {
        // Récupération des données du formulaire
        $formData = $request->request->all();
        // si le formulaire est vide on quitte
        if (empty($formData)) {
            return new JsonResponse([
                'error' => 'formData empty'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        // s'il y' une valeur dans le champ du token on la sauvegarde
        // sinon token vaut null
        $token = $formData['search_form']['_token'] ?? null;
        // si le token du formulaire existe
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
      
        // on récupère la valeur des champs
        $searchTerm = $formData['search_form']['search_term_desktop']|| $formData['search_form']['search_term_mobile'];
        $this->logger->info('Term search:', ['search_form[...]' => $searchTerm]);
        
        // $submittedFormType = $jsonData['submitted_form_type'] ?? null;
        // $this->logger->info('Form submitted:', ['submitted_form_type' => $submittedFormType]);



        // Récupération des résultats de recherche pour les ServiceItems
        $queryBuilder = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
        // $queryBuilder = $this->entityManager->getRepository(Theme::class)->findAll();

        $priceFilter = $formData['search_form']['price_filter'] ?? null;
        $this->logger->info('Price:', ['priceFilter' => $priceFilter]);

        // on trie les résultats
        if ($priceFilter === 'low_to_high') {
            $queryBuilder->orderBy('si.price', 'ASC');
        } elseif ($priceFilter === 'high_to_low') {
            $queryBuilder->orderBy('si.price', 'DESC');
        }
        // on recherche le resultat
        $serviceItems = $queryBuilder->getQuery()->getResult();
        // On sérialise un tableau d'objet complexe
        // on traite également les références circulaire et attribuons
        // un ID unique pour éviter les boucles infinie 
        try {

            $results = $serializer->serialize($serviceItems, JsonEncoder::FORMAT);

            $this->logger->info('Serialized results:', ['results' => $results]);

            return new JsonResponse($results, 200, [], true);

        } catch (\Exception $e) {
            $this->logger->error('Serialization error:', ['exception' => $e]);
            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }*/
}
