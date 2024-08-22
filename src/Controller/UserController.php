<?php

namespace App\Controller;
// Importation des classes nécessaires

use App\Entity\User;
use App\Entity\Order;
use App\Form\UserType;
use App\Service\UserAnonymizer;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Form\ServiceItemType;
use App\Service\ImageService;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Chunk\LastChunk;

class UserController extends AbstractController
{
    private $logger;
    private $entityManager;
    private $orderRepository;
    private $userRepository;
    private $urlGenerator;
    private $imageService;
    private $security;
    private $userAnonymizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        OrderRepository $orderRepository,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        ImageService $imageService,
        Security $security,
        UserAnonymizer $userAnonymizer
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->imageService = $imageService;
        $this->security = $security;
        $this->userAnonymizer = $userAnonymizer;
    }

    #[Route('/user/list', name: 'list_users')]
    public function index(UserRepository $userRepository): Response
    {

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController'
        ]);
    }

    #[Route('/user/detail/{id}', name: 'detail_user')]
    public function detailsUser(?User $user = null): Response
    {
        // On vérifie que service est bien une instance
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // on utilise imageService pour générer l'url imgae utilisateur
        $this->imageService->setPictureUrl($user);
        // Récupérer l'utilisateur par ID
        // $user = $userRepository->find($id);
        return $this->render('user/index.html.twig', [
            'title_page' => 'Détail de l\'utilisateur',
            'controller_name' => 'UserController',
            'lastDeveloper' => null,
            'lastClient' => null,
            'orders_pending' => null,
            'orders_completed' => null,
            'user' => $user

        ]);
    }


    #[Route('/profile/edit/{id}/{service}', name: 'profile_edit')]
    public function edit(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        PaginatorInterface $paginator,
        ?ServiceItem $service = null

    ): Response {

        $limit = 3;
        $formHasErrors = false;

        $page = $request->get('page');

        if (!$page) {
            $page = 1;
        }
        // Récupérer l'utilisateur par ID
        $user = $userRepository->find($id);
        // s'il y'a une action AJAX sur la pagination 
        if ($request->isXmlHttpRequest()) {

            $status = $request->get('status');

            $orders = $entityManager->getRepository(Order::class)->findByUserIdAndStatus($id, $status);

            $pagination = $paginator->paginate(
                $orders,
                $page,
                $limit
            );
            // On rends juste la liste des Orders avec pagination
            $formHtml = $this->renderView('/user/order/' . $status . '.html.twig', [
                'orders_' . $status =>  $pagination->getItems(),
                'pagination_' . $status => $pagination
            ]);

            // Retourner la réponse JSON 
            return new JsonResponse(['orders' => $formHtml, 'type_order' => 'orders_' . $status], Response::HTTP_OK);
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

        $this->logger->info('Processing generateImageUrl user curent', ['user' => $user, 'role' => $role, 'originalFilename' => $originalFilename]);

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

        if (!$service) {
            $service = new ServiceItem();
        }

        // obtenir le nom du fichier de l'image de profil de l'utilisateur
        // $originalFilename = $service->getPicture();
        // if ($originalFilename) {
        //     $this->imageService->setPictureUrl($service);
        // }

        $formService = $this->createForm(ServiceItemType::class, $service, [
            'action' => $this->generateUrl('service_form_update'),
        ]);

        $formService->handleRequest($request);

        // // traite le formulaire de service
        if ($formService->isSubmitted()) {
            if (!$formService->isValid()) {
                $formHasErrors = true;
            } else {
                // récupérer l'objet file
                $file = $formService->get('picture')->getData();
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
                // associe le service à l'utilisateur
                $service->setUser($user);
                $entityManager->persist($service);
                $entityManager->flush();
                $this->addFlash('success', 'Service ajouté avec succès');
                return $this->redirectToRoute('profile_edit', ['id' => $user->getId()]);
            }
        }
        /**
         * REACT COMPONENT
         */
        // Récupérer le dernier utilisateur avec le rôle ROLE_CLIENT
        $lastClient = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_CLIENT');
        $this->logger->info('lastClient', ['user' => $lastClient->getQuery()->getResult()]);
        // Récupérer le dernier utilisateur avec le rôle ROLE_DEVELOPER
        $lastDeveloper = $this->entityManager->getRepository(User::class)->findOneUserByRole('ROLE_DEVELOPER');
        $this->logger->info('lastDeveloper', ['user' => $lastDeveloper->getQuery()->getResult()]);


        $developer = $lastDeveloper->getQuery()->getSingleResult();
        $this->imageService->setPictureUrl($developer, 'ROLE_DEVELOPER');

        $lastDeveloperData = $serializer->serialize($developer, 'json', ['groups' => 'user']);
        $dataDeveloper = json_decode($lastDeveloperData, true);

        $client = $lastClient->getQuery()->getSingleResult();

        $this->imageService->setPictureUrl($client, 'ROLE_CLIENT');

        $lastClientData = $serializer->serialize($client, 'json', ['groups' => 'user']);
        $dataClient = json_decode($lastClientData, true);
        /**
         *  recherche des commandes nouvelles
         */
        //
        $status = 'pendding';
        $ordersPending = $entityManager->getRepository(Order::class)->findByUserIdAndStatus($id, $status);

        $paginationPending = $paginator->paginate(
            $ordersPending,
            $page,
            $limit
        );
        /**
         * recherche des commandes complètes
         */
        $status = 'completed';
        $ordersCompleted = $entityManager->getRepository(Order::class)->findByUserIdAndStatus($id, $status);

        $paginationCompleted = $paginator->paginate(
            $ordersCompleted,
            $page,
            $limit
        );

        return $this->render('user/index.html.twig', [
            'title_page' => 'Profil',
            'formUser' => $formUser->createView(),
            'errorsFormUser' => $formUser->getErrors(true),
            'formAddService' => $formService->createView(),
            'serviceId' => $service->getId(),
            'errorsFormService' => $formService->getErrors(true),
            'orders_pending' =>  $paginationPending->getItems(),
            'pagination_pending' => $paginationPending,
            'orders_completed' => $paginationCompleted->getItems(),
            'pagination_completed' => $paginationCompleted,
            'lastDeveloper' => $dataDeveloper,
            'lastClient' => $dataClient,
            'formHasErrors' => $formHasErrors
        ]);
    }



    // supprimer tous les services d'un utilisateur après avoir supprimer son propre compte
    // sauvegarde les données utilisateur pour les factures / commandes
    #[Route('/user/delete/anonymize', name: 'anonymize_user')]
    public function anonymizeAndDelete()
    {
        $user = $this->getUser();

        if (!$user) {
            throw new \Exception('User not found.');
        }
        $this->addFlash('warning', 'Anonymisation des données utilisateur');

        $this->userAnonymizer->anonymizeUser($user);
    }

    /**
     * Affiche la liste des Développeurs ou des Client suivant le $role en argument
     */
    #[Route('/usertype/list/{role}/{page}', name: 'list_user_type')]
    public function listUserType(String $role, int $page, Request $request, PaginatorInterface $paginator, SerializerInterface $serializer): Response
    {
        $limit = 3;
        // Récupération les User par le role: ROLE_CLIENT
        $queryBuilder = $this->entityManager->getRepository(User::class)->findUsersByRole($role);
        // On filtre par username et l'on trie
        $queryBuilder->orderBy('u.username', 'ASC');
        // On recherche les résultats
        $users = $queryBuilder->getQuery()->getResult();

        $pagination = $paginator->paginate(
            $users,
            $page,
            $limit
        );
        foreach ($users as $user) {
            $this->imageService->setPictureUrl($user);
        }
        return $this->render('client/index.html.twig', [
            'users' => $pagination,
            'pagination' => $pagination,
            'title_page' => 'Liste des utilisateurs',
            'role' => $role
        ]);
    }
}
