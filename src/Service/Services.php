<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Services extends AbstractController
{
    private $entityManager;
    private $imageService;
    private $serializer;
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ImageService $imageService,
        LoggerInterface $logger,
    ) {
        $this->entityManager = $entityManager;
        $this->imageService = $imageService;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function getLastServices10() :array
    {
        $serviceRepository = $this->entityManager->getRepository(ServiceItem::class);

        $services10 = $serviceRepository->findby([], ['id' => 'DESC'], 10);

        $this->logger->info('Service: ', ['getLastServices10' => $services10]);

        if ($services10) {
            // Définir les URL des images pour chaque service
            foreach ($services10 as $service) {
                $this->imageService->setPictureUrl($service);
            }
            // on sérialize
            $servicesJSON = $this->serializer->serialize($services10, 'json', ['groups' => 'serviceItem']);
            // convertit la chaine en tableau associatif
            $services10 = json_decode($servicesJSON, true);
        }

        return $services10 ? $services10 : [];
    }
}
