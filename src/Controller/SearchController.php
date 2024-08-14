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
use Symfony\Component\Form\Extension\Core\Type\SearchType;
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
            'title_page' => 'Recherche de services avancées',
            'service_count' => $ServiceCount,
        ]);
    }

    #[Route("/search/results/formdata/navbar", name: "search_results_formdata_navbar", methods: ['POST'])]
    public function searchResultFormDataNavbar(Request $request, SerializerInterface $serializer, ThemeRepository $themeRepository)
    {
        // Vérifier le Content-Type de la requête
        $contentType = $request->headers->get('Content-Type');

        if (strpos($contentType, 'multipart/form-data') === false) {
            return new JsonResponse(['error' => 'Invalid Content-Type'], 415);
        }

        $formData = $request->request->all();
        $this->logger->info('Received form data', ['formData' => $formData]);

        $searchForm = $formData['search_form'];
        $searchTerm = '';

        $submittedToken = $searchForm['_token'] ?? $request->request->get('_token', '');

        if (isset($searchForm['search_term_mobile']) && !empty($searchForm['search_term_mobile'])) {
            $searchTerm = $searchForm['search_term_mobile'];
            $tokenName = 'search_form';
        } elseif (isset($formData['search_term_desktop']) && !empty($searchForm['search_term_desktop'])) {
            $searchTerm = $searchForm['search_term_desktop'];
            $tokenName = 'search_form';
        } else {
            $searchTerm = $request->request->get('search_term', '');
            $tokenName = 'token_search_term';
        }
        // // le nom du token créer par twig dans la vue
        $csrfToken = new CsrfToken($tokenName, $submittedToken);

        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            $this->logger->info('error:', ['Invalid CSRF token' => $csrfToken]);
            return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Vérification et traitement du terme rechercher
        if (!empty($searchTerm)) {
            $searchTerm = trim($searchTerm);

            // Validation avec Symfony Validator
            $validator = Validation::createValidator();
            $violations = $validator->validate($searchTerm, [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 3]),
            ]);

            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                $this->logger->error('Validation errors:', ['errors' => $errors]);
                return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Validation passed successfully.');
            $searchTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
        } else {
            return new JsonResponse(['error' => 'Search term is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Construction de la requête
        $queryBuilder = $themeRepository->searchByTermAllChilds($searchTerm);
        $this->logger->info('Generated SQL Query:', ['sql' => $queryBuilder]);

        // Sérialisation des résultats
        try {
            $serializedResults = $serializer->serialize($queryBuilder, 'json', ['groups' => 'serviceItem']);
            $this->logger->info('Serialized results:', ['results' => $serializedResults]);
            return new JsonResponse($serializedResults, 200, [], true);
        } catch (\Exception $e) {
            $this->logger->error('Serialization error:', ['exception' => $e]);
            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route("/search/results/formdata", name: "search_results_formdata", methods: ['POST'])]
    public function searchResultFormData(Request $request, SerializerInterface $serializer, ThemeRepository $themeRepository)
    {
        // Vérifier le Content-Type de la requête
        $contentType = $request->headers->get('Content-Type');

        if (strpos($contentType, 'multipart/form-data') === false) {
            return new JsonResponse(['error' => 'Invalid Content-Type'], 415);
        }

        $formData = $request->request->all();
        $this->logger->info('Received form data', ['formData' => $formData]);

        $submittedToken = $formData['_token'] ?? $request->request->get('_token', '');
        // // le nom du token créer par twig dans la vue
        $csrfToken = new CsrfToken('token_search_term', $submittedToken);

        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            $this->logger->info('error:', ['Invalid CSRF token' => $csrfToken]);
            return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
        }

        $searchTerm = $formData['search_term'] ?? '';

        // Vérification et traitement du terme rechercher
        if (!empty($searchTerm)) {
            $searchTerm = trim($searchTerm);

            // Validation avec Symfony Validator
            $validator = Validation::createValidator();
            $violations = $validator->validate($searchTerm, [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 3]),
            ]);

            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                $this->logger->error('Validation errors:', ['errors' => $errors]);
                return new JsonResponse(['error' => $errors], JsonResponse::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Validation passed successfully.');
            $searchTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');
        } else {
            return new JsonResponse(['error' => 'Search term is required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Construction de la requête
        $queryBuilder = $themeRepository->searchByTermAllChilds($searchTerm);
        $this->logger->info('Generated SQL Query:', ['sql' => $queryBuilder]);

        // Sérialisation des résultats
        try {
            $serializedResults = $serializer->serialize($queryBuilder, 'json', ['groups' => 'serviceItem']);
            $this->logger->info('Serialized results:', ['results' => $serializedResults]);
            return new JsonResponse($serializedResults, 200, [], true);
        } catch (\Exception $e) {
            $this->logger->error('Serialization error:', ['exception' => $e]);
            return new JsonResponse(['error' => 'Serialization error'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // recherche un developpeur par username, lastName, firstName ou par city
    #[Route("/fetch/search/developer/by", name: "search_developer", methods: ['POST'])]
    public function searchDeveloper(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->logger->info('Received JSON data:', ['data' => $data]);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON data'], JsonResponse::HTTP_BAD_REQUEST);
        }
        // on recherche le token soumis dans le formulaire
        $submittedToken = $data['_token'];
        $this->logger->info('Submitted token:', ['csrf_token' => $submittedToken]);
        // on recherche le searchTerm du formulaire
        $searchTerm = $data['search-user-by-name'] ?? '';

        if ($searchTerm) {
            // on crée un token du même nom que celui créer dans le template twig
            // searchItemUserToken: "{{ csrf_token('searchItemUserToken') }}",
            $csrfToken = new CsrfToken('searchItemUserToken', $submittedToken);
            $this->logger->info('CSRF Token for validation:', ['csrf_token' => $csrfToken]);
            // on vérfie a l'aide du gestionnaire de token qu'il correspond a celui stocké 
            // en session
            if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
                $this->logger->error('Invalid CSRF token:', ['csrf_token_name' => $csrfToken]);
                return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
            }
            // on effectue la recherche
            $users = $this->entityManager->getRepository(User::class)->searchByTerm($searchTerm, "ROLE_DEVELOPER");
            $this->logger->info('Received users by name:', ['users' => $users]);
        }
        // on recherche le searchTerm du formulaire
        $searchTerm = $data['search-user-by-city'] ?? '';

        if ($searchTerm) {
            // on crée un token du même nom que celui créer dans le template twig
            // searchItemUserToken: "{{ csrf_token('searchItemUserToken') }}",
            $csrfToken = new CsrfToken('searchItemCityToken', $submittedToken);
            $this->logger->info('CSRF Token for validation:', ['csrf_token' => $csrfToken]);
            // on vérfie a l'aide du gestionnaire de token qu'il correspond a celui stocké 
            // en session
            if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
                $this->logger->error('Invalid CSRF token:', ['csrf_token_name' => $csrfToken]);
                return new JsonResponse(['error' => 'Invalid CSRF token'], JsonResponse::HTTP_FORBIDDEN);
            }
            // on effectue la recherche
            $users = $this->entityManager->getRepository(User::class)->searchByTermFromCity($searchTerm, "ROLE_DEVELOPER");
            $this->logger->info('Received users by city:', ['users' => $users]);
        }
        // on utilise le imageService pour générer les liens image
        foreach ($users as $user) {
            $this->imageService->setPictureUrl($user);
        }
        // on sérialise les résultatet les convertissons au format JSON
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
    public function searchClient(Request $request): JSONResponse
    {
        return new JSONResponse("test");
    }
}
