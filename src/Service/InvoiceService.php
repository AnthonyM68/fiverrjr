<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Invoice;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TCPDF;

class InvoiceService
{
    private $security;
    private $parameters;

    private $logger;
    public function __construct(Security $security, ParameterBagInterface $parameters, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->parameters = $parameters;
        $this->logger = $logger;
    }


    public function generateInvoicePdf($invoiceData): string
    {
        // Déterminez le chemin de sauvegarde du fichier PDF
        $directory = $this->parameters->get('invoices_client');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . '/invoice_' . $invoiceData['order']->getId() . '.pdf';

        // Création du PDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Titre
        $pdf->Cell(0, 10, 'Invoice Details', 0, 1, 'C');

        // Détails de la commande
        $order = $invoiceData['order'];
        $pdf->Cell(0, 10, 'Order ID: ' . $order->getId(), 0, 1);
        $pdf->Cell(0, 10, 'Date of Order: ' . $order->getDateOrder()->format('Y-m-d H:i:s'), 0, 1);
        $pdf->Cell(0, 10, 'Status: ' . $order->getStatus(), 0, 1);

        // Détails des services
        $pdf->Cell(0, 10, 'Services:', 0, 1);
        foreach ($order->getServices() as $serviceItem) {
            $pdf->Cell(0, 10, '- ' . $serviceItem->getTitle() . ' - ' . $serviceItem->getPrice() . ' EUR', 0, 1);
        }


        $totalAmount = $order->getPayment();
        $pdf->Cell(0, 10, 'Total Amount: ' . $totalAmount . ' EUR', 0, 1);

        // Détails de paiement
        $payment = $invoiceData['payment'];
        $pdf->Cell(0, 10, 'Payment ID: ' . $payment->getId(), 0, 1);
        $pdf->Cell(0, 10, 'Payment Date: ' . $payment->getDatePayment()->format('Y-m-d H:i:s'), 0, 1);

        // Sauvegarde du fichier PDF
        $pdf->Output($filePath, 'F');

        return $filePath;
    }

    public function createInvoice(Order $order, string $pdfPath): Invoice
    {
        // pour obtenir l'utilisateur authentifié depuis le invoiceService
        $user = $this->security->getUser();
        // on s'assure que $user soit bien une instance de User
        if ($user instanceof User) {
            // on crée une nouvelle facture
            $invoice = new Invoice();
            // on lie la commande à la facture
            $invoice->setOrderRelation($order);
            $this->logger->info('invoice', ['invoice' =>  $invoice]);

            // on recherche le payment
            $payment = $order->getPayment();
            // si le paiement existe
            if ($payment) {
                // recherche le montant du paiement
                $invoice->setAmount($payment->getAmount());
                // on set le status a payé
                $invoice->setStatus('paid');
            } else {
                // Axe d'amélioration
                // s'il n'ya pas de payment
                $invoice->setAmount('0.00');
                // status en attente
                $invoice->setStatus('pennding');
            }
            $invoice->setTva('20.00');
            $invoice->setDateCreate(new \DateTime());
            $invoice->setPdfPath($pdfPath);
            return $invoice;
        }

        throw new \Exception('Utilisateur non authentifié.');
    }
}
