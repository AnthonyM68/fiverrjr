<?php

namespace App\Controller;
// Importation des classes nécessaires

use App\Entity\ServiceItem;
use App\Entity\User;
use App\Form\UserType;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use App\Service\ImageUploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
    public function edit(User $user, Request $request, ImageUploaderInterface $imageUploader): Response
    {
        // On s'assure que $user est bien une instance de User et qu'il existe
        if (!$user) {
            $user = new user();
        }
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
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
        // dd($form->getData());
        return $this->render('user/index.html.twig', [
            'title_page' => 'profil',
            'form' => $form->createView(),
            'user' => $user
        ]);
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

    #[Route('/enterprise', name: 'home_enterprise')]
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
}
