<?php

namespace App\Service;

use App\Entity\User;
use App\Service\ImageService;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserService extends AbstractController
{
    private $entityManager;
    private $imageService;
    private $serializer;
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        ImageService $imageService,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->imageService = $imageService;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    public function getLastUser(?string $role): array
    {
        // recherche un utilisateur par rôle
        $userRepository = $this->entityManager->getRepository(User::class);
        $lastUser = $userRepository->findLastUserByRole($role);
        $this->logger->info('UserService: ', ['getLastUser' => $lastUser]);

        if ($lastUser) {
            $this->imageService->setPictureUrl($lastUser);
            // Définir les URL des images pour chaque service
            $lastUserJSON = $this->serializer->serialize($lastUser, 'json', ['groups' => 'user']);
             // convertit la chaine en tableau associatif
            $lastUser = json_decode($lastUserJSON, true);
        }

        return $lastUser ? $lastUser : [];
    }
}
