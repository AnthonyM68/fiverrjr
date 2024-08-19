<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\User;
use App\Service\Cart;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    private $entityManager;
    private $logger;
    private $imageService;

    // Constructeur pour injecter l'EntityManager
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ImageService $imageService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->imageService = $imageService;
    }


    /**
     * Route pour la page d'accueil
     *
     * @return Response
     */


    #[Route('/home', name: 'home')]
    public function index(
        Cart $cart,
        Request $request,
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

        $pictureFilename = $developer->getPicture();
        if ($pictureFilename) {
            $pictureUrl = $this->imageService->generateImageUrl($pictureFilename, 'ROLE_DEVELOPER');
            $developer->setPicture($pictureUrl);
        }
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

        $pictureFilename = $client->getPicture();
        if ($pictureFilename) {
            $pictureUrl = $this->imageService->generateImageUrl($pictureFilename, 'ROLE_CLIENT');
            $client->setPicture($pictureUrl);
        }
        $this->logger->info('client', [
            'client' => $client
        ]);
        return new JsonResponse($client, JsonResponse::HTTP_OK);
    }
}
