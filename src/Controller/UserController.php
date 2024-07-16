<?php

namespace App\Controller;
// Importation des classes nécessaires

use App\Entity\User;
use App\Entity\Order;
use App\Form\UserType;
use App\Form\OrderType;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;

use App\Service\ImageUploaderInterface;



use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends AbstractController
{
    private $logger;
    private $entityManager;
    private $orderRepository;
    private $userRepository;
    private $urlGenerator;


    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/user/list', name: 'list_users')]
    public function index(UserRepository $userRepository): Response
    {
        // Récupère tous les users de la base de données
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }
    #[Route('/user/edit/{id}', name: 'edit_user')]
    public function editUser(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/detail/{id}', name: 'edit_user')]
    public function detailsUser(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    // #[Route('/update_user_role/{userId}', name: 'update_user_role')]
    // public function updateUserRole(UserRepository $userRepository, int $userId): Response
    // {
    //     // Récupérer l'utilisateur depuis le repository (exemple)
    //     $userUpdate = $userRepository->find($userId);

    //     if (!$userUpdate) {
    //         throw $this->createNotFoundException('Utilisateur non trouvé');
    //     }
    //     // Mettre à jour le rôle de l'utilisateur
    //     $userUpdate->setRoles(['ROLE_ADMIN']);

    //     // Persister les modifications
    //     $this->entityManager->persist($userUpdate);
    //     $this->entityManager->flush();

    //     // Redirection ou réponse
    //     return $this->redirectToRoute('list_users');
    // }



    #[Route('/profile/edit/{id}', name: 'profile_edit')]
    public function edit(?User $user, Request $request, ImageUploaderInterface $imageUploader): Response
    {
        // On s'assure que $user est bien une instance de User et qu'il existe
        if (!$user) {
            $user = new user();
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $orders = $this->orderRepository->findBy(['userId' => $user->getId()]);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On supprime l'image actuel, upload la nouvelle et persiste la nouvelle url
            // ( ImageUploaderService )
            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                $imageUploader->uploadImage($pictureFile, $user);
            }
            // On enregistre en base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre profil à bien été mis à jour');
            // On redirige sur le profil avec en paramètre l'id user, l'objet pour affiché 
            // à nouveau les infos de l'user
            return $this->redirectToRoute('profile_edit', ['id' => $user->getId()]);
        }

        $lastDeveloper = $this->userRepository->findOneUserByRole("ROLE_DEVELOPER");
        // dd($lastDeveloper);
        return $this->render('user/index.html.twig', [
            'title_page' => 'profil',
            'form' => $form->createView(),
            'user' => $user,
            'orders' => $orders,
            'lastDeveloper' => $lastDeveloper
        ]);
    }


    #[Route('/api/lastDeveloper', name: 'api_lastDeveloper', methods: ['GET'])]
    public function lastDeveloper(SerializerInterface $serializer): JsonResponse
    {
        $lastDeveloper = $this->userRepository->findOneUserByRole("ROLE_DEVELOPER");
        $lastDeveloper = $lastDeveloper->getQuery()->getOneOrNullResult();

        if ($lastDeveloper) {
            $pictureFilename = $lastDeveloper->getPicture();

            // On utilise le controller pour fournir le chemin absolu de l'image ( services.yaml )
            if ($pictureFilename) {
                $pictureUrlResponse = $this->forward('App\Controller\ImageController::generateImageUrl', [
                    'filename' => $pictureFilename,
                ]);
                $pictureUrl = json_decode($pictureUrlResponse->getContent(), true)['url']; // Extract the URL from JSON response
            }
            // On format les données avant de retourner à Javascript
            $developerData = [
                'id' => $lastDeveloper->getId(),
                'firstName' => $lastDeveloper->getFirstName(),
                'lastName' => $lastDeveloper->getLastName(),
                'email' => $lastDeveloper->getEmail(),
                'username' => $lastDeveloper->getUsername(),
                'picture' =>  $pictureUrl,
                'dateRegister' => $lastDeveloper->getDateRegister(),
                'city' => $lastDeveloper->getCity(),
                'portfolio' => $lastDeveloper->getPortfolio(),
                'bio' => $lastDeveloper->getBio(),
            ];

            $jsonDeveloperData = $serializer->serialize($developerData, 'json');
            return new JsonResponse($jsonDeveloperData, 200, [], true);
        } else {
            return new JsonResponse(['error' => 'No developer found'], 404);
        }
    }



    #[Route('/developer', name: 'home_developer')]
    public function developer(): Response
    {
        // Récupération les User par le role: ROLE_ENTERPRISE
        $queryBuilder = $this->entityManager->getRepository(User::class)->findUsersByRole("ROLE_DEVELOPER");
        // On filtre par username et l'on trie
        $queryBuilder->orderBy('u.username', 'ASC');
        // On recherche les résultats
        $users = $queryBuilder->getQuery()->getResult();

        $this->logger->info('UserController:line:98', [
            'ROLE_ENTERPRISE' => $users
        ]);
        return $this->render('developer/index.html.twig', [
            'title_page' => 'Liste des Développeurs',
            'developers' => $users
        ]);
    }



    #[Route('/developer/order', name: 'list_orders_developer')]
    public function orders(UserRepository $userRepository): Response
    {

        return $this->render('user/orders/index.html.twig', [
            'title_page' => 'Vos commandes'
        ]);
    }
    #[Route('/developer/new/invoice', name: 'new_invoice_developer')]
    public function newInvoice(?Order $order = null, UserRepository $userRepository): Response
    {
        if (!$order) {
            $order = new ServiceItem();
        }
        // Variable pour stocker les erreurs de validation
        $errors = null;
        // Crée et gère le formulaire pour le service
        $form = $this->createForm(OrderType::class, $order);

        if ($form->isSubmitted()) {
            // Si le formulaire est valide, persiste et sauvegarde la Category
            if ($form->isValid()) {
            }
        }
        return $this->render('user/invoice/index.html.twig', [
            'title_page' => 'Créer une facture'
        ]);
    }




    #[Route('/client', name: 'home_client')]
    public function enterprise(Request $request): Response
    {
        // Récupération les User par le role: ROLE_ENTERPRISE
        $queryBuilder = $this->entityManager->getRepository(User::class)->findUsersByRole("ROLE_ENTERPRISE");
        // On filtre par username et l'on trie
        $queryBuilder->orderBy('u.username', 'ASC');
        // On recherche les résultats
        $users = $queryBuilder->getQuery()->getResult();

        $this->logger->info('UserController:line:98', [
            'ROLE_ENTERPRISE' => $users
        ]);
        return $this->render('enterprise/index.html.twig', [
            'title_page' => 'Liste des Entreprises',
            'enterprises' => $users
        ]);
    }

    #[Route('/client/invoice', name: 'list_invoice_client')]
    public function invoices(UserRepository $userRepository): Response
    {
        return $this->render('user/invoices/index.html.twig', [
            'title_page' => 'Vos factures'
        ]);
    }

    #[Route('/client/order/add', name: 'new_order_client')]
    public function newOrder(?Order $order = null, UserRepository $userRepository, Request $request): Response
    {
        if (!$order) {
            $order = new Order();
        }
        // Variable pour stocker les erreurs de validation
        $errors = null;
        // Crée et gère le formulaire pour le service
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Si le formulaire est valide, persiste et sauvegarde l'Order
            if ($form->isValid()) {
                $this->entityManager->persist($order);
                $this->entityManager->flush();
                $this->addFlash('success', 'Votre commande sera ajoutée au panier');
                // Redirige vers la liste des thèmes après sauvegarde
                return $this->redirectToRoute('list_services');
            }
        }
        return $this->render('user/orders/index.html.twig', [
            'title_page' => 'Nouvelle commande',
            'formAddOrder' => $form->createView(),
            'errors' => $form->getErrors(true),
        ]);
    }
}
