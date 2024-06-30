<?php

namespace App\Controller;
// Importation des classes nécessaires

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
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
    #[Route('/update_user_role/{userId}', name: 'update_user_role')]
    public function updateUserRole(UserRepository $userRepository, EntityManagerInterface $entityManager, int $userId): Response
    {
        // Récupérer l'utilisateur depuis le repository (exemple)
        $userUpdate = $userRepository->find($userId);
        
        if (!$userUpdate) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
        // Mettre à jour le rôle de l'utilisateur
        $userUpdate->setRoles(['ROLE_ADMIN']);
        
        // Persister les modifications
        $entityManager->persist($userUpdate);
        $entityManager->flush();
        
        // Redirection ou réponse
        return $this->redirectToRoute('list_users');
    }
    #[Route('/profile', name: 'profile')]
    public function getProfile(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
