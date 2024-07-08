<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\ServiceItem;
use App\Entity\Category;
use App\Form\SearchFormType;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use App\Repository\ServiceItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $entityManager;
    private $logger;
    // Constructeur pour injecter l'EntityManager
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    /**
     * Route pour la page d'accueil
     *
     * @return Response
     */
    #[Route('/home', name: 'home')]
    public function index(Request $request): Response
    {
        // Données pour le carousel sur le home
        // Récupérer le dernier utilisateur avec le rôle ROLE_ENTERPRISE
        $lastEnterprise = $this->entityManager->getRepository(User::class)->findOneUsersByRole('ROLE_ENTERPRISE');
        // Récupérer le dernier utilisateur avec le rôle ROLE_DEVELOPER
        $lastDeveloper = $this->entityManager->getRepository(User::class)->findOneUsersByRole('ROLE_DEVELOPER');
        // // Récupérer le dernier service ajouté
        // $lastService = $this->entityManager->getRepository(ServiceItem::class)->findOneBy([], ['id' => 'DESC']);
        // Enregistrement des données de la requête dans les logs
        $this->logger->info('HomeController: line:49 Résults Search', [
            'lastDeveloper' => $lastDeveloper,
<<<<<<< HEAD
            // 'lastService' => $lastService
=======
            'lastEnterprise' =>  $lastEnterprise,
            'lastService' => $lastService
>>>>>>> ab4038126793de0d041a51225717c263819f881d
        ]);
        return $this->render('home/index.html.twig', [
            // Données pour le carousel sur le home
            'lastDeveloper' => $lastDeveloper,
<<<<<<< HEAD
            // 'lastService' => $lastService,
=======
            'lastEnterprise' => $lastEnterprise,
            'lastService' => $lastService,

>>>>>>> ab4038126793de0d041a51225717c263819f881d
            'submitted_form' => null,
            
            'title_page' => 'Accueil'
        ]);
    }


    // #[Route('/admin', name: 'admin')]
    // public function administrator(): Response
    // {
    //     return $this->render('administrator/index.html.twig', [
    //         'title_page' => 'Tableau de bord'
    //     ]);
    // }
}
