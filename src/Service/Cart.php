<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServiceItemRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class Cart extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private $session;
    private $serviceItemRepository;

    public function __construct(
    SessionInterface $session, 
    ServiceItemRepository $serviceItemRepository,
    EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->serviceItemRepository = $serviceItemRepository;
        $this->session = $session;
    }

    #[Route('/cart', name: 'cart')]
    public function getCart(): array
    {
        dd();
        $panier = $this->session->get('cart', []);

        $data = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $product = $this->serviceItemRepository->find($id);
            $data[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }
        return compact('data', 'total');
    }
}
