<?php
namespace App\Service;

use App\Entity\User;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;
    private $imageService;

    public function __construct(EntityManagerInterface $entityManager, ImageService $imageService)
    {
        $this->entityManager = $entityManager;
        $this->imageService = $imageService;
    }

    public function getLastUser(?string $role)
    {
        // recherche un utilisateur par rôle
        $userRepository = $this->entityManager->getRepository(User::class);
        $lastClient = $userRepository->findOneUserByRole($role);

        if ($lastClient) {
            $this->imageService->setPictureUrl($lastClient, $role);
        }

        return $lastClient;
    }
}
