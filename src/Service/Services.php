<?php

namespace App\Service;

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

    public function get()
    {
        
    }
}
