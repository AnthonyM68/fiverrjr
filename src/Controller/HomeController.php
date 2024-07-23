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

 
        $fullCart = $cart->getCart($request);
        // dd($fullCart);
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
