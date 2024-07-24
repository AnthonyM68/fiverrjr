<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\User;
use App\Service\Cart;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $entityManager;
    private $logger;

    // Constructeur pour injecter l'EntityManager
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }


    /**
     * Route pour la page d'accueil
     *
     * @return Response
     */


    #[Route('/home', name: 'home')]
    public function index(Cart $cart, Request $request): Response
    {
        // Récupérer le dernier utilisateur avec le rôle ROLE_ENTERPRISE
        $lastEnterprise = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_ENTERPRISE');
        // Récupérer le dernier utilisateur avec le rôle ROLE_DEVELOPER
        $lastDeveloper = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_DEVELOPER');
        // // Récupérer le dernier service ajouté
        $lastService = $this->entityManager->getRepository(ServiceItem::class)->findOneBy([], ['id' => 'DESC']);



        // Enregistrement des données de la requête dans les logs
        $this->logger->info('HomeController: line:49 Résults Search', [
            'lastDeveloper' => $lastDeveloper,
            'lastEnterprise' => $lastEnterprise,
            'lastService' => $lastService
        ]);

        // Récupérer le cookie de la requête
        $cookieName = 'panier_cookie';
        $cookieValue = $request->cookies->get($cookieName);

        // dd($cookieValue);

        // Désérialiser les données du panier à partir du cookie
        // if ($cookieValue) {
        //     $fullCart = $serializer->deserialize($cookieValue, Cart::class, 'json');
        // } else {
        //     $fullCart = $cart->getCart($request);
        // }

        // Enregistrer le cookie dans les logs pour vérification
        $this->logger->info('Cookie Info', [
            'cookieName' => $cookieName,
            'cookieValue' => $cookieValue
        ]);
        // setcookie("TestCookie", "", time() - 3600, "/");
        // $fullCart = $cart->getCart($request);

        // Récupérer les cookies
        // $cookieName = 'cart'; // Remplacez par le nom de votre cookie
        // $cookieValue = $request->cookies->get($cookieName);

        // // Enregistrer le cookie dans les logs pour vérification
        // $this->logger->info('Cookie Info', [
        //     'cookieName' => $cookieName,
        //     'cookieValue' => $cookieValue
        // ]);

        // calcul la sommes des valeurs du tableau
        // $totalItems = array_sum($fullCart['totalServiceItem']);

        return $this->render('home/index.html.twig', [
            // Données pour le carousel sur le home
            'lastDeveloper' => $lastDeveloper,
            'lastEnterprise' => $lastEnterprise,
            'lastService' => $lastService,
            'submitted_form' => null,
            'title_page' => 'Accueil'
        ]);
    }
}
