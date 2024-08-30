<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Service\UserService;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $logger;
    private $imageService;
    private $entityManager;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        ImageService $imageService,
    ) {
        $this->entityManager = $entityManager;
        $this->imageService = $imageService;
        $this->serializer = $serializer;
        $this->logger = $logger;
        
    }

    #[Route('/home', name: 'home')]
    public function index(UserService $userService, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager): Response {

        $lastClient = $userService->getLastUser('ROLE_CLIENT');
        // si un client est trouvé
        if ($lastClient) {
            // on sérialize l'objet incluant la convertion JSON
            $lastClientData = $this->serializer->serialize($lastClient, 'json', ['groups' => 'user']);
            // convertit la chaine JSON en un tableau associatif PHP manipulatble
            $dataClient = json_decode($lastClientData, true);
        } 

        $lastDeveloper = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_DEVELOPER');
        if($lastDeveloper) {
            $lastDeveloperData = $this->serializer->serialize($lastDeveloper, 'json', ['groups' => 'user']);
            $dataDeveloper = json_decode($lastDeveloperData, true);
        }

        $lastService = $this->entityManager->getRepository(ServiceItem::class)->findby([], ['id' => 'DESC'], 10);
        // Définir les URL des images pour chaque service
        foreach ($lastService as $service) {
            $this->imageService->setPictureUrl($service);
        }
        $lastServiceData = $this->serializer->serialize($lastService, 'json', ['groups' => 'serviceItem']);
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
            'title_page' => 'Accueil',
            'description' => '<meta name="description" content="Fiverr Junior - La plateforme idéale pour les jeunes freelances. Trouvez des opportunités de travail, développez vos compétences et lancez votre carrière en ligne. Rejoignez une communauté dynamique où des talents émergents de vos meilleurs projet." />'
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
        ]);
        return new JsonResponse($client, JsonResponse::HTTP_OK);
    }
}
