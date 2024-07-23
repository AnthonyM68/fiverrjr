<?php

namespace App\Service;

use App\Entity\ServiceItem;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServiceItemRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class Cart extends AbstractController
{
    private $entityManager;
    private $serviceItemRepository;

    public function __construct(
        ServiceItemRepository $serviceItemRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
        $this->serviceItemRepository = $serviceItemRepository;
    }


    public function getCart(Request $request)
    {
        $session = $request->getSession();

        $panier = $session->get('cart', []);

        $data = [];
        $total = 0;

        foreach ($panier as $id => $quantity) {
            $product = $this->serviceItemRepository->find($id);
            if ($product) {
                $data[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
            }
        }
        return compact('data', 'total');
    }

    public function addProduct(ServiceItem $serviceItem, Request $request)
    {
        $session = $request->getSession();
        // On récupère l'id du produit
        $id = $serviceItem->getId();
    
        // On récupère le panier existant
        $panier = $session->get('cart', []);
    
        // On ajoute le produit dans le panier s'il n'y est pas encore
        // Sinon on incrémente sa quantité
        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }
    
        $session->set('cart', $panier);
    }

    public function removeProduct(ServiceItem $serviceItem, Request $request)
    {
        $session = $request->getSession();
        //On récupère l'id du produit
        $id = $serviceItem->getId();

        // On récupère le panier existant
        $panier = $session->get('cart', []);

        // On retire le produit du panier s'il n'y a qu'1 exemplaire
        // Sinon on décrémente sa quantité
        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        $session->set('cart', $panier);
        
        //On redirige vers la page du panier
        return $this->redirectToRoute('cart_product');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteProduct(ServiceItem $serviceItem, Request $request)
    {
        $session = $request->getSession();
        //On récupère l'id du produit
        $id = $serviceItem->getId();

        // On récupère le panier existant
        $panier = $session->get('cart', []);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        $session->set('cart', $panier);
        
        //On redirige vers la page du panier
        return $this->redirectToRoute('cart_product');
    }

    public function empty(Request $request)
    {
        $session = $request->getSession();
        $session->remove('cart');

        return $this->redirectToRoute('cart_product');
    }
}
