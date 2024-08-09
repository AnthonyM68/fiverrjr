<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Theme;
use PHPUnit\Util\Json;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Service\ImageService;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

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
    public function searchResult(Request $request, SerializerInterface $serializer, ThemeRepository $themeRepository)
    {
        // on récupère les  données JSON envoyer par js
        $data = json_decode($request->getContent(), true);
        // on vérfie si la convertion échoue
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $this->logger->info('Received JSON data:', ['data' => $data]);

        // on vérifie le token CSRF
        $submittedToken = $data['_token'];
        $this->logger->info('Submitted token:', ['submittedToken' => $submittedToken]);

        $csrfToken = new CsrfToken('token_search_term', $submittedToken);

        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            // si la vérification  échoue on quitte
            return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
            $this->logger->info('error:', ['Invalid CSRF token' => $csrfToken]);
        }
        // on recherche le terme de rechercher
        $searchTerm = $data['search_term'] ?? null;
        $this->logger->info('searchTerm (before processing):', ['searchTerm' => $searchTerm]);
        // Vérification et traitement du terme rechercher
        if ($searchTerm !== null) {
            // Nettoyage du terme de recherche
            $searchTerm = trim($searchTerm);
            // Validation avec Symfony Validator
            $validator = Validation::createValidator();
            $violations = $validator->validate($searchTerm, [
                // on défini des contraintes
                new Assert\NotBlank(),
                new Assert\Length(['min' => 3]),
                new Assert\Regex(['pattern' => '/^[A-Za-z0-9\- éèàùêâîôûçÉÈÀÙÊÂÎÔÛÇ]+$/u', 'message' => 'Invalid characters in search term.'])
            ]);
            // s'il y a des violations de contraintes, on les ajoutes
            // comme erreur et retournons le résultat a javascript
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                return new JsonResponse(['error' => '$errors'], JsonResponse::HTTP_BAD_REQUEST);
            }
            // échappe les caractères spéciaux en tenant compte des accents
            $searchTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
            $this->logger->info('searchTerm (after processing):', ['searchTerm' => $searchTerm]);
        }
        // Construction de la requête
        $queryBuilder = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
        $this->logger->info('Generated SQL Query:', ['sql' => $queryBuilder]);
        // on sérialise les résultats de recherches d'objets complexe avec gestionnaire circulaire
        try {
            $serializedResults = $serializer->serialize($queryBuilder, JsonEncoder::FORMAT, ['groups' => 'serviceItem']);
            $this->logger->info('Serialized results:', ['results' => $serializedResults]);
            return new JsonResponse($serializedResults, 200, [], true);
        } catch (\Exception $e) {
            $this->logger->error('Serialization error:', ['exception' => $e]);
            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    #[Route("/search/developer/name", name: "search_developer", methods: ['POST'])]
    public function searchDeveloper(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->logger->info('Received JSON data:', ['data' => $data]);

        $submittedToken = $data['_token'];
        $this->logger->info('Submitted token:', ['csrf_token_user' => $submittedToken]);

        $csrfToken = new CsrfToken('searchItemUserToken', $submittedToken);

        // if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
        //     return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
        //     $this->logger->info('error:', ['Invalid CSRF token' => $csrfToken]);
        // }


        // // Suppose that $searchTerm is part of $data
        $searchTerm = $data['search-user-by-name'] ?? '';

        // Your logic to fetch users based on $searchTerm
        $users = $this->entityManager->getRepository(User::class)->searchByTerm($searchTerm, "ROLE_DEVELOPER");
        // on utilise le imageService pour générer le lien image
        foreach($users as $user) {
            $this->imageService->setPictureUrl($user);
        }
        try {
            $results = $this->serializer->serialize($users, JsonEncoder::FORMAT, ['groups' => 'user']);
            $this->logger->info('Serialized results:', ['results' => $results]);
            return new JsonResponse($results, 200, [], true);
        } catch (\Exception $e) {
            $this->logger->error('Serialization error:', ['exception' => $e]);
            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route("/search/client/name", name: "search_client", methods: ['POST'])]
    public function searchClient(Request $request): JsonResponse
    {
        // Récupération des données du formulaire
        $formData = $request->request->all();

        $searchTerm = $formData['search-user-by-name'];
        $token = $formData['_token'];
        // Vérifier le token CSRF
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('search_item_user', $token))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
        }
        $users = $this->entityManager->getRepository(User::class)->searchByTerm($searchTerm, "ROLE_DEVELOPER");

        foreach ($users as $user) {
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
