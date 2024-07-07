<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\User;
use App\Form\UserType;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $logger;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
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
    #[Route('/user/edit/{id}', name: 'edit_user')]
    public function detailsUser(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/update_user_role/{userId}', name: 'update_user_role')]
    public function updateUserRole(UserRepository $userRepository, int $userId): Response
    {
        // Récupérer l'utilisateur depuis le repository (exemple)
        $userUpdate = $userRepository->find($userId);

        if (!$userUpdate) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        // Mettre à jour le rôle de l'utilisateur
        $userUpdate->setRoles(['ROLE_ADMIN']);

        // Persister les modifications
        $this->entityManager->persist($userUpdate);
        $this->entityManager->flush();

        // Redirection ou réponse
        return $this->redirectToRoute('list_users');
    }


    #[Route('/profile/edit/{id}', name: 'profile_edit')]
    public function edit(User $user, Request $request, UserPasswordHasherInterface  $userPasswordHasher): Response
    {
        // On s'assure que $user est bien une instance de User et qu'il existe
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('L\'utilisateur doit être une instance de User.');
        }
        // On crée une nouvelle instance du formulaire user avec l'utilistateur 
        // courrent récupéré en argument à la méthode
        // permet pré-remplissage des champs
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données du champ picture
            $pictureFile = $form->get('picture')->getData();
            // Si les données existe
            if ($pictureFile) {
                // On créer un nom nom uniue
                $newFilename = uniqid() . '.' . $pictureFile->guessExtension();
                // On détermine le répertoire en fonction du rôle
                $uploadDirectory = $this->getParameter('pictures_directory');
                if (in_array('ROLE_DEVELOPER', $user->getRoles())) {
                    $uploadDirectory = $this->getParameter('developer_pictures_directory');
                } elseif (in_array('ROLE_ENTERPRISE', $user->getRoles())) {
                    $uploadDirectory = $this->getParameter('enterprise_pictures_directory');
                }
                // On essaye de déplacé l'image
                try {
                    $pictureFile->move(
                        $uploadDirectory,
                        $newFilename
                    );
                    $user->setPicture($newFilename);
                } catch (FileException $e) {
                    throw new \LogicException('Une erreur s\'est produite lors du téléchargement de l\'image.');
                }
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil à bien été mis à jour');
            // On redirige sur le profil avec en paramètre l'id user, l'objet pour affiché 
            // à nouveau les infos de l'user
            return $this->redirectToRoute('profile_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/index.html.twig', [
            'title_page' => 'Votre profil',
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
