<?php

namespace App\Service;

use TCPDF;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\Invoice;
use App\Entity\Payment;
use Psr\Log\LoggerInterface;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InvoiceService
{
    private $security;
    private $parameters;
    private $orderRepository;
    private $logger;
    private $entityManager;
    private $serializer;

    public function __construct(
        Security $security,
        ParameterBagInterface $parameters,
        LoggerInterface $logger,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        $this->security = $security;
        $this->parameters = $parameters;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
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
        // font github thème fiverr junior not work here ???
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

        // Récupération et affichage du montant total
        $payment = $order->getPayment();
        if ($payment instanceof Payment) {
            $pdf->Cell(0, 10, 'Total Amount: ' . $payment->getAmount() . ' EUR', 0, 1);

            // Détails de paiement
            $pdf->Cell(0, 10, 'Payment ID: ' . $payment->getId(), 0, 1);
            $pdf->Cell(0, 10, 'Payment Date: ' . $payment->getDatePayment()->format('d-m-Y H:i:s'), 0, 1);
        } else {
            $pdf->Cell(0, 10, 'Total Amount: 0.00 EUR', 0, 1);
        }



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

    public function downloadPdf($id): Response
    {
        $order = $this->orderRepository->find($id);

        if (!$order instanceof Order) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Order not found.');
        }


        $invoice = $order->getInvoice();
        // if (!$invoice instanceof Invoice) {
        //     throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Invoice not found for this order.');
        // }

        if (!$this->security->isGranted('ROLE_CLIENT', $order)) {
            throw new AccessDeniedException('Access Denied');
        }

        $filePath = $invoice->getPdfPath();

        if (!file_exists($filePath)) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('File not found.');
        }

        $response = new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        });

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');

        return $response;
    }
    public function deleteCompletedInvoice(int $id): bool
    {
        $order = $this->entityManager->getRepository(Order::class)->find($id);

        if (!$order instanceof Order) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Order not found.');
        }

        $invoice = $order->getInvoice();

        if (!$invoice instanceof Invoice) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Invoice not found.');
        }

        if (!$this->security->isGranted('ROLE_CLIENT', $order)) {
            throw new AccessDeniedException('Access Denied');
        }

        if ($order->getStatus() === 'completed') {
            $this->archiveInvoiceData($invoice, $order);

            $this->entityManager->persist($order);
            $this->entityManager->remove($invoice);
            $this->entityManager->flush();

            $this->logger->info('Invoice deleted successfully.', ['invoiceId' => $id]);
            return true;
        } else {
            $this->logger->info('Invoice not deleted. Order status is not "completed".', ['invoiceId' => $id]);
            return false;
        }
    }

    private function archiveInvoiceData(Invoice $invoice, Order $order): void
    {
        $orderData = $this->serializer->serialize($order, 'json', ['groups' => 'order']);
        $orderSerialize = json_decode($orderData, true);

        $payment = $order->getPayment();
        $paymentData = $this->serializer->serialize($payment, 'json', ['groups' => 'payment']);
        $paymentSerialize = json_decode($paymentData, true);

        $user = $order->getUser();
        $userData = $user ? $this->serializer->serialize($user, 'json', ['groups' => 'userTraceability']) : [];
        
        $invoice->setOrderTraceability(json_encode([
            'order' => $orderSerialize,
            'payment' => $paymentSerialize,
        ]));

        $invoice->setClientTraceability(json_encode([
            'invoice_id' => $invoice->getId(),
            'user' => $userData,
        ]));

        $this->entityManager->persist($invoice);
    }
}
