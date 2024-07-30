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
use App\Service\ImageService;

use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\ServiceItemRepository;
use Knp\Component\Pager\PaginatorInterface;
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
    private $imageService;


    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        ImageService $imageService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->imageService = $imageService;
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
    #[Route('/profile/edit/{id}/{page}', name: 'profile_pagination')]
    public function edit(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        PaginatorInterface $paginator,
        int $page = null
    ): Response {

        if (!$page) {

            if (!is_int($page) || $page < 1) {
                $page = 1;
            }
        }
        // Récupérer l'utilisateur par ID
        $user = $userRepository->find($id);
        // s'il y'a une action AJAX sur la pagination 
        if ($request->isXmlHttpRequest()) {
            $limit = 6;
            $orders = $this->entityManager->getRepository(Order::class)->findBy(['userId' => $id]);
            $pagination = $paginator->paginate(
                $orders,
                $page,
                $limit
            );
            // On rends juste la liste des Orders avec pagination
            $formHtml = $this->renderView('/user/order/index.html.twig', [
                'orders' => $pagination,
                'pagination' => $pagination,
            ]);
            // Retourner la réponse JSON 
            return new JsonResponse(['formHtml' => $formHtml], Response::HTTP_OK);
        }

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        // détermine le répertoire de destination de l'image en fonction du role de l'utilisateur
        // permet de construire l'url de l'image
        $roles = $user->getRoles();
        $role = match (true) {
            in_array('ROLE_ADMIN', $roles, true) => 'ROLE_ADMIN',
            in_array('ROLE_CLIENT', $roles, true) => 'ROLE_CLIENT',
            in_array('ROLE_DEVELOPER', $roles, true) => 'ROLE_DEVELOPER',
            default => 'ROLE_USER',
        };
        // obtenir le nom du fichier de l'image de profil de l'utilisateur
        $originalFilename = $user->getPicture();

        $this->logger->info('Processing fetch user curent', ['user' => $user, 'role' => $role, 'originalFilename' => $originalFilename]);

        if ($originalFilename) {
            try {
                $pictureUrl = $this->imageService->generateImageUrl($originalFilename, $role);
                $user->setPicture($pictureUrl);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        // crée le formulaire pour l'utilisateur
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {

            // récupérer l'objet file
            $file = $formUser->get('picture')->getData();
            // si un fichier est téléchargé, traiter le fichier
            // si $file est bien une instance de UploadedFile ( ServiceItemType )
            if ($file instanceof UploadedFile) {
                try {
                    // On supprime l'image actuelle
                    $this->imageService->deleteImage($originalFilename, $role);
                    // On déplace la nouvelle et récupère son nom et extention
                    $fileName = $this->imageService->uploadImage($file, $role);
                    // On set a l'user
                    $user->setPicture($fileName);
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

        // créer un nouveau formulaire ServiceItem
        $service = new ServiceItem();
        $formService = $this->createForm(ServiceItemType::class, $service);
        $formService->handleRequest($request);

        // // traite le formulaire de service
        if ($formService->isSubmitted() && $formService->isValid()) {
            // associe le service à l'utilisateur
            $service->setUser($user);
            // $entityManager->persist($service);
            // $entityManager->flush();
            $this->addFlash('success', 'Service ajouté avec succès');
            return $this->redirectToRoute('profile_edit', ['id' => $user->getId()]);
        }


        /**
         * REACT COMPONENT
         */
        // Récupérer le dernier utilisateur avec le rôle ROLE_ENTERPRISE
        $lastClient = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_CLIENT');
        // Récupérer le dernier utilisateur avec le rôle ROLE_DEVELOPER
        $lastDeveloper = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_DEVELOPER');


        $developer = $lastDeveloper->getQuery()->getSingleResult();

        $this->imageService->setPictureUrl($developer, 'ROLE_DEVELOPER');

        $lastDeveloperData = $serializer->serialize($developer, 'json', ['groups' => 'user']);
        $dataDeveloper = json_decode($lastDeveloperData, true);

        $client = $lastClient->getQuery()->getSingleResult();

        $this->imageService->setPictureUrl($client, 'ROLE_CLIENT');

        $lastClientData = $serializer->serialize($client, 'json', ['groups' => 'user']);
        $dataClient = json_decode($lastClientData, true);

        $limit = 6;
        // Récupérer les dernieres Commandes Order ajoutés
        $orders = $this->entityManager->getRepository(Order::class)->findby(['userId' => $id]);
        //  dd($orders);
        $pagination = $paginator->paginate(
            $orders,
            $page,
            $limit
        );


        return $this->render('user/index.html.twig', [
            'title_page' => 'Profil',
            'formUser' => $formUser->createView(),
            'errorsFormUser' => $formUser->getErrors(true),
            'formAddService' => $formService->createView(),
            'errorsFormService' => $formService->getErrors(true),
            'orders' => $pagination,
            'pagination' => $pagination,
            'lastDeveloper' => $dataDeveloper,
            'lastClient' => $dataClient

        ]);
    }



    #[Route('/usertype/list/{role}/{page}', name: 'list_user_type')]
    public function listUserType(String $role, int $page, Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        $limit = 6;
        // Récupération les User par le role: ROLE_CLIENT
        $queryBuilder = $this->entityManager->getRepository(User::class)->findUsersByRole($role);
        // On filtre par username et l'on trie
        $queryBuilder->orderBy('u.username', 'ASC');
        // On recherche les résultats
        $users = $queryBuilder->getQuery()->getResult();

        $this->logger->info('List Clients', [
            $role => $users
        ]);
        $pagination = $paginator->paginate(
            $users,
            $page,
            $limit
        );

        foreach ($users as $user) {
            $pictureFilename = $user->getPicture();
            $this->logger->info('Processing user', ['user' => $user->getUsername(), 'pictureFilename' => $pictureFilename]);
            if ($pictureFilename) {
                try {
                    $pictureUrl = $this->imageService->generateImageUrl($pictureFilename, $role);
                    $this->logger->info('Generated picture URL', [
                        'user' => $user->getUsername(),
                        'pictureUrl' => $pictureUrl
                    ]);
                    $user->setPicture($pictureUrl);
                } catch (\Exception $e) {
                    $this->logger->error('Failed to generate picture URL', [
                        'service' => $user->getUsername(),
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }
        }

        // Définir le titre de la page selon le rôle
        $titlePage = ($role === 'ROLE_DEVELOPER') ? 'Liste des Développeurs' : 'Liste des Entreprises';

        return $this->render('client/index.html.twig', [
            'users' => $pagination,
            'pagination' => $pagination,
            'title_page' => $titlePage,
            'role' => $role
        ]);
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


    /**
     * recherche les dernier user inscrit Component React Profil 
     */

    #[Route('/last/user/{role}', name: 'api_lastDeveloper', methods: ['GET'])]
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
}
