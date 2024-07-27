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
    private $httpKernel;
    private $logger;

    public function __construct(
        ServiceItemRepository $serviceItemRepository,
        EntityManagerInterface $entityManager,
        HttpKernelInterface $httpKernel,
        LoggerInterface $logger,
    ) {
        $this->entityManager = $entityManager;
        $this->serviceItemRepository = $serviceItemRepository;
        $this->httpKernel = $httpKernel;
        $this->logger = $logger;
    }

    public function getCart(Request $request)
    {
        $session = $request->getSession();

        $panier = $session->get('cart', []);

        $data = [];
        $total = 0;
        $totalServiceItem = 0;

        foreach ($panier as $id => $quantity) {
            $product = $this->serviceItemRepository->find($id);

            if ($product) {
                $pictureUrl = '';
                
                $pictureFilename = $product->getPicture();
                if ($pictureFilename) {

                    $subRequest = $request->duplicate(null, null, [
                        '_controller' => 'App\Controller\ImageController::generateImageUrl',
                        'filename' => $pictureFilename,
                        'usertype' => 'SERVICE'

                    ]);
                    $this->logger->info('pictureUrl: ' . $subRequest);
                    $pictureUrlResponse = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
                    $pictureUrl = json_decode($pictureUrlResponse->getContent(), true);
                }
                $data[] = [
                    'serviceItem' => [
                        'id' => $product->getId(),
                        'title' => $product->getTitle(),
                        'price' => $product->getPrice(),
                        'picture' => $pictureUrl
                    ],
                    'quantity' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
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
        // $pictureFilename = $serviceItem->getPicture();
        // if ($pictureFilename) {

        //     $subRequest = $request->duplicate(null, null, [
        //         '_controller' => 'App\Controller\ImageController::generateImageUrl',
        //         'filename' => $pictureFilename,
        //         'usertype' => 'SERVICE'

        //     ]);
        //     $pictureUrlResponse = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        //     $pictureUrl = json_decode($pictureUrlResponse->getContent(), true);
        // }
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
