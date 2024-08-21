<?php
namespace App\Service;

use App\Entity\Order;
use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function generateInvoice(Order $order): Invoice
    {
        $invoice = new Invoice();
        $invoice->setOrder($order);
        $invoice->setAmount($order->getTotal()); // Assurez-vous d'avoir une méthode getTotal() dans l'entité Order
        $invoice->setDate(new \DateTime());

        // Persister et flush l'entité
        $this->em->persist($invoice);
        $this->em->flush();

        return $invoice;
    }
}