<?php

namespace App\Service;

use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServiceItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Cart
{
    private $entityManager;
    private $serviceItemRepository;
    private $imageService;
    private $httpKernel;
    private $logger;

    public function __construct(
        ServiceItemRepository $serviceItemRepository,
        EntityManagerInterface $entityManager,
        HttpKernelInterface $httpKernel,
        LoggerInterface $logger,
        ImageService $imageService
    ) {
        $this->entityManager = $entityManager;
        $this->serviceItemRepository = $serviceItemRepository;
        $this->httpKernel = $httpKernel;
        $this->logger = $logger;
        $this->imageService = $imageService;
    }

    public function getCart(Request $request)
    {
        $session = $request->getSession();

        $panier = $session->get('cart', []);

        $data = [];
        $total = 0;
        $totalServiceItem = 0;

        foreach ($panier as $id => $quantity) {
            $service = $this->serviceItemRepository->find($id);

            if ($service) {
                $pictureUrl = '';
                
                $originalFilename = $service->getPicture();
                $this->logger->info('Processing generateImageUrl service cart', ['originalFilename' => $originalFilename]);

                if ($originalFilename) {

                    if ($originalFilename) {
                        try {
                            $pictureUrl = $this->imageService->generateImageUrl($originalFilename, 'SERVICE');
                            // on set l'url de l'image 
                            $service->setPicture($pictureUrl);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                    }

                }
                $data[] = [
                    'serviceItem' => [
                        'id' => $service->getId(),
                        'title' => $service->getTitle(),
                        'price' => $service->getPrice(),
                        'picture' => $pictureUrl
                    ],
                    'quantity' => $quantity
                ];
                $total += $service->getPrice() * $quantity;
                $totalServiceItem += $quantity;
            }
        }

        return [
            'data' => $data,
            'total' => $total,
            'totalServiceItem' => $totalServiceItem
        ];
    }

    public function addProduct(ServiceItem $serviceItem, Request $request)
    {
        $session = $request->getSession();
        $id = $serviceItem->getId();
        $panier = $session->get('cart', []);

        if (isset($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('cart', $panier);
    }

    public function removeProduct(ServiceItem $serviceItem, Request $request)
    {
        $session = $request->getSession();
        $id = $serviceItem->getId();
        $panier = $session->get('cart', []);

        if (isset($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('cart', $panier);
    }

    public function deleteProduct(ServiceItem $serviceItem, Request $request)
    {
        $session = $request->getSession();
        $id = $serviceItem->getId();
        $panier = $session->get('cart', []);

        if (isset($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('cart', $panier);
    }

    public function empty(Request $request)
    {
        $session = $request->getSession();
        $session->remove('cart');
    }
}
