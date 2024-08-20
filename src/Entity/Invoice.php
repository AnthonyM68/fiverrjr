<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $tva = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreate = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    private ?string $clientFirstName = null;

    #[ORM\Column(length: 50)]
    private ?string $clientLastName = null;

    #[ORM\Column(length: 255)]
    private ?string $clientAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $clientEmail = null;

    #[ORM\Column(nullable: true)]
    private ?array $orderTraceability = null;

    #[ORM\Column(nullable: true)]
    private ?array $invoiceTraceability = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(string $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTimeInterface $dateCreate): static
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getClientFirstName(): ?string
    {
        return $this->clientFirstName;
    }

    public function setClientFirstName(string $clientFirstName): static
    {
        $this->clientFirstName = $clientFirstName;

        return $this;
    }

    public function getClientLastName(): ?string
    {
        return $this->clientLastName;
    }

    public function setClientLastName(string $clientLastName): static
    {
        $this->clientLastName = $clientLastName;

        return $this;
    }

    public function getClientAddress(): ?string
    {
        return $this->clientAddress;
    }

    public function setClientAddress(string $clientAddress): static
    {
        $this->clientAddress = $clientAddress;

        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(string $clientEmail): static
    {
        $this->clientEmail = $clientEmail;

        return $this;
    }

    public function getOrderTraceability(): ?array
    {
        return $this->orderTraceability;
    }

    public function setOrderTraceability(?array $orderTraceability): static
    {
        $this->orderTraceability = $orderTraceability;

        return $this;
    }

    public function getInvoiceTraceability(): ?array
    {
        return $this->invoiceTraceability;
    }

    public function setInvoiceTraceability(?array $invoiceTraceability): static
    {
        $this->invoiceTraceability = $invoiceTraceability;

        return $this;
    }
}
