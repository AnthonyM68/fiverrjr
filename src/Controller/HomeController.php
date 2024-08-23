<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\User;
use App\Service\CartService;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
// use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    private $entityManager;
    private $logger;
    private $imageService;
    // private $security;
    // private $session;
    // Constructeur pour injecter l'EntityManager
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ImageService $imageService,
        // Security $security,
        // SessionInterface $session
        
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->imageService = $imageService;
        // $this->security = $security;
        // $this->session = $session;
    }
    /**
     * Route pour la page d'accueil
     *
     * @return Response
     */
    #[Route('/home', name: 'home')]
<<<<<<< HEAD
    public function index(Request $request): Response
    {
        // Création et gestion du formulaire de recherche
        $formTheme = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'theme',
            'search_label' => 'Recherchez votre service',
        ]);
        $formTheme->handleRequest($request);
        $results = [];
        $searchTerm= null;
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
=======
    public function index(
        SerializerInterface $serializer,
        CsrfTokenManagerInterface $csrfTokenManager,
        UrlGeneratorInterface $urlGenerator

    ): Response {

        // Récupérer le dernier utilisateur avec le rôle ROLE_ENTERPRISE
        $lastClient = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_CLIENT');
        // Récupérer le dernier utilisateur avec le rôle ROLE_DEVELOPER
        $lastDeveloper = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_DEVELOPER');
        // // Récupérer le dernier service ajouté
        $lastService = $this->entityManager->getRepository(ServiceItem::class)->findby([], ['id' => 'DESC'], 10);

        $developer = $lastDeveloper->getQuery()->getSingleResult();

        $this->imageService->setPictureUrl($developer);
        $lastDeveloperData = $serializer->serialize($developer, 'json', ['groups' => 'user']);
        $dataDeveloper = json_decode($lastDeveloperData, true);

        $client = $lastClient->getQuery()->getSingleResult();
        $this->imageService->setPictureUrl($client, 'ROLE_CLIENT');
        $lastClientData = $serializer->serialize($client, 'json', ['groups' => 'user']);
        $dataClient = json_decode($lastClientData, true);

        // Définir les URL des images pour chaque service
        foreach ($lastService as $service) {
            $this->imageService->setPictureUrl($service);
        }
        $lastServiceData = $serializer->serialize($lastService, 'json', ['groups' => 'serviceItem']);
        $dataService = json_decode($lastServiceData, true);

        // Générez les tokens 
        $csrfTokenUser = $csrfTokenManager->getToken('csrf_token_user')->getValue();
        $csrfTokenCity = $csrfTokenManager->getToken('csrf_token_city')->getValue();
        // Générez les URLs
        $searchDeveloper = $urlGenerator->generate('search_developer');
        $searchClient = $urlGenerator->generate('search_client');

        return $this->render('home/index.html.twig', [
            'lastDeveloper' => $dataDeveloper,
            'lastClient' => $dataClient,
            'lastService' => $dataService,
            'searchItemUserToken' => $csrfTokenUser,
            'searchItemCityToken' => $csrfTokenCity,
            'search_developer' => $searchDeveloper,
            'search_client' => $searchClient,
            'submitted_form' => null,
            'title_page' => 'Accueil'
        ]);
    }

    #[Route('/api/lastDeveloper', name: 'api_last_developer')]
    public function getLastDeveloper(SerializerInterface $serializer): JsonResponse
    {
        $lastDeveloper = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_DEVELOPER');
        $developer = $lastDeveloper->getQuery()->getSingleResult();
        $this->imageService->setPictureUrl($developer);

        $jsonFullCart = $serializer->serialize($developer, 'json', ['groups' => 'serviceItem']);
        $this->logger->info('developer', [
            'developer' => $jsonFullCart
        ]);
        return new JsonResponse($developer, JsonResponse::HTTP_OK);
    }

    #[Route('/api/lastClient', name: 'api_last_client')]
    public function getLastClient(): JsonResponse
    {
        $lastClient = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_CLIENT');
        $client = $lastClient->getQuery()->getSingleResult();

        $this->imageService->setPictureUrl($client);
        $this->logger->info('client', [
            'client' => $client
>>>>>>> a5feb3db027be62ad942fe5c640558f052dbbba0
        ]);
        return new JsonResponse($client, JsonResponse::HTTP_OK);
    }
}
