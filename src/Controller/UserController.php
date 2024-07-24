<?php

namespace App\Controller;
// Importation des classes nécessaires

use App\Entity\User;
use App\Entity\Order;
use App\Form\UserType;
use App\Form\OrderType;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Form\ServiceItemType;
use App\Repository\UserRepository;

use App\Repository\OrderRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServiceItemRepository;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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

    #[Route('/profile/edit/{id}', name: 'profile_edit')]
    public function edit(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {

        // Récupérer l'utilisateur par ID
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // crée le formulaire pour l'utilisateur
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);
        // détermine le répertoire de destination de l'image en fonction du role de l'utilisateur
        // permet de construire l'url de l'image
        $roles = $user->getRoles();
        $role = match (true) {
            in_array('ROLE_ADMIN', $roles, true) => 'ROLE_ADMIN',
            in_array('ROLE_CLIENT', $roles, true) => 'ROLE_CLIENT',
            in_array('ROLE_DEVELOPER', $roles, true) => 'ROLE_DEVELOPER',
            default => 'ROLE_USER',
        };

        // créer un nouveau formulaire ServiceItem
        $service = new ServiceItem();
        $formService = $this->createForm(ServiceItemType::class, $service);
        $formService->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            // obtenir le nom du fichier de l'image de profil de l'utilisateur
            $userPictureFilename = $user->getPicture();
            // récupérer l'objet file
            $file = $formUser->get('picture')->getData();
            // si un fichier est téléchargé, traiter le fichier
            // si $file est bien une instance de UploadedFile ( ServiceItemType )
            if ($file instanceof UploadedFile) {
                try {
                    // supprime l'image actuelle de l'utilisateur ( User ) si elle existe
                    $apiResponse = $this->forward('App\Controller\ImageController::deleteImage', [
                        'filename' => $userPictureFilename,
                        'role' => $role,
                    ]);

                    // Décoder la réponse JSON
                    $apiResponse = json_decode($apiResponse->getContent(), true);
                    // si une erreur est retournée, on affiche un message flash d'erreur
                    if (isset($apiResponse['error'])) {
                        $this->addFlash('error', $apiResponse['error']);
                    } else {
                        // télécharger la nouvelle image
                        $apiResponse = $this->forward('App\Controller\ImageController::uploadImage', [
                            'file' => $file,
                            'role' => $role,
                        ]);

                        // Décoder la réponse JSON
                        $apiResponse = json_decode($apiResponse->getContent(), true);

                        // si une erreur est retournée, on affiche un message flash d'erreur
                        if (isset($apiResponse['error'])) {
                            $this->addFlash('error', $apiResponse['error']);
                        } else {
                            // mettre à jour l'utilisateur avec le nouveau nom de fichier de l'image
                            $user->setPicture($apiResponse['filename']);
                        }
                    }
                } catch (\Exception $e) {
                    // si une exception est levée, afficher un message flash d'erreur
                    $this->addFlash('error', 'Une erreur s\'est produite lors du traitement de l\'image: ' . $e->getMessage());
                }
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a bien été mis à jour');
            return $this->redirectToRoute('profile_edit', ['id' => $user->getId()]);
        }
        // traite le formulaire de service
        if ($formService->isSubmitted() && $formService->isValid()) {
            // associe le service à l'utilisateur
            $service->setUser($user);
            $entityManager->persist($service);
            $entityManager->flush();
            $this->addFlash('success', 'Service ajouté avec succès');
            return $this->redirectToRoute('profile_edit', ['id' => $user->getId()]);
        }
        // récupére le nom de fichier de l'image de l'utilisateur
        $pictureFilename = $user->getPicture();
        // on utilise le controller pour fournir le chemin absolu de l'image ( config: services.yaml )
        if ($pictureFilename) {
            $pictureUrlResponse = $this->forward('App\Controller\ImageController::generateImageUrl', [
                'filename' => $pictureFilename,
                'role' => $role
            ]);
            $pictureUrl = json_decode($pictureUrlResponse->getContent(), true);
        }

        return $this->render('user/index.html.twig', [
            'title_page' => 'Profil',
            'formUser' => $formUser->createView(),
            'errors_form' => $formUser->getErrors(true),
            'formAddService' => $formService->createView(),
            'errors_formService' => $formService->getErrors(true),
            'orders' => '',
            // on rend l'image du profilen chemin absolu
            'pictureUrl' => $pictureUrl['url'] ?? null,

        ]);
    }
    #[Route('/developer', name: 'home_developer')]
    public function developer(): Response
    {
        // Récupération des User par le role: ROLE_DEVELOPER
        $queryBuilder = $this->entityManager->getRepository(User::class)->findUsersByRole("ROLE_DEVELOPER");
        // on filtre par username et on trie
        $queryBuilder->orderBy('u.username', 'ASC');
        // on recherche les résultats
        $users = $queryBuilder->getQuery()->getResult();
        return $this->render('developer/index.html.twig', [
            'title_page' => 'Liste des Développeurs',
            'developers' => $users
        ]);
    }
    #[Route('/api/last/{role}', name: 'api_lastDeveloper', methods: ['GET'])]
    public function lastDeveloper(String $role, SerializerInterface $serializer): JsonResponse
    {

        // récupère les users par role et trie par dateRegister
        $lastUser = $this->userRepository->findOneUserByRole($role);
        // recherche le dernier des users
        $lastUser = $lastUser->getQuery()->getOneOrNullResult();
        // s'il existe
        if ($lastUser) {
            // on recherche son image de profil
            $pictureFilename = $lastUser->getPicture();
            // on utilise le controller pour fournir le chemin absolu de l'image ( services.yaml )
            if ($pictureFilename) {
                $pictureUrlResponse = $this->forward('App\Controller\ImageController::generateImageUrl', [
                    'filename' => $pictureFilename,
                    'role' => $role
                ]);
                $pictureUrl = json_decode($pictureUrlResponse->getContent(), true);
            }
            // On format les données avant de retourner à Javascript
            $lastUserData = [
                'id' => $lastUser->getId(),
                'firstName' => $lastUser->getFirstName(),
                'lastName' => $lastUser->getLastName(),
                'email' => $lastUser->getEmail(),
                'username' => $lastUser->getUsername(),
                'picture' =>  $pictureUrl['url'],
                'dateRegister' => $lastUser->getDateRegister(),
                'city' => $lastUser->getCity(),
                'portfolio' => $lastUser->getPortfolio(),
                'bio' => $lastUser->getBio(),
            ];
            // on sérialize les données et les convertis en JSON
            $jsonDeveloperData = $serializer->serialize($lastUserData, 'json');
            return new JsonResponse($jsonDeveloperData, 200, [], true);
        } else {
            return new JsonResponse(['error' => 'No developer found'], 404);
        }
    }

    #[Route('/developer/order', name: 'list_orders_developer')]
    public function orders(): Response
    {
        return $this->render('user/orders/index.html.twig', [
            'title_page' => 'Vos commandes'
        ]);
    }

    #[Route('/developer/new/invoice', name: 'new_invoice_developer')]
    public function newInvoice(?Order $order = null): Response
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
    public function client(): Response
    {
        // Récupération les User par le role: ROLE_CLIENT
        $queryBuilder = $this->entityManager->getRepository(User::class)->findUsersByRole("ROLE_CLIENT");
        // On filtre par username et l'on trie
        $queryBuilder->orderBy('u.username', 'ASC');
        // On recherche les résultats
        $users = $queryBuilder->getQuery()->getResult();

        $this->logger->info('UserController:line:98', [
            'ROLE_CLIENT' => $users
        ]);
        return $this->render('client/index.html.twig', [
            'title_page' => 'Liste des Entreprises',
            'clients' => $users
        ]);
    }
    #[Route('/client/invoice', name: 'list_invoice_client')]
    public function invoices(): Response
    {
        return $this->render('user/invoices/index.html.twig', [
            'title_page' => 'Vos factures'
        ]);
    }

    #[Route('/client/order/add', name: 'new_order_client')]
    public function newOrder(Request $request, ?Order $order): Response
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
