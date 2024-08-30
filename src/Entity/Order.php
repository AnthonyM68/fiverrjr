<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['order'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOrder = null;

    #[ORM\Column]
    private ?string $status = null;

    #[Groups(['order'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDelivery = null;
    /**
     * Undocumented variable
     *
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    /**
     * Undocumented variable
     *
     * @var Invoice|null
     */
    #[ORM\OneToOne(mappedBy: 'orderRelation', targetEntity: Invoice::class)]
    private ?Invoice $invoice = null;
    /**
     * Undocumented variable
     *
     * @var Collection
     */
    #[ORM\OneToMany(targetEntity: ServiceItem::class, mappedBy: 'order')]
    private Collection $services;
    /**
     * Undocumented variable
     *
     * @var Payment|null
     */
    #[ORM\OneToOne(targetEntity: Payment::class, mappedBy: 'orderRelation')]
    private ?Payment $payment = null;


    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): static
    {
        $this->payment = $payment;

        return $this;
    }
    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->dateOrder;
    }

    public function setDateOrder(\DateTimeInterface $dateOrder): static
    {
        $this->dateOrder = $dateOrder;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateDelivery(): ?\DateTimeInterface
    {
        return $this->dateDelivery;
    }

    public function setDateDelivery(\DateTimeInterface $dateDelivery): static
    {
        $this->dateDelivery = $dateDelivery;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Collection<int, ServiceItem>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(ServiceItem $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }

        return $this;
    }

    public function removeService(ServiceItem $service): static
    {
        $this->services->removeElement($service);

        return $this;
    }
}
