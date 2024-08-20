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

    #[ORM\Column]
    #[Groups(['order'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?int $serviceId = null;

    #[ORM\Column]
     #[Groups(['order'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?int $userId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
     #[Groups(['order'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?\DateTimeInterface $dateOrder = null;

    #[ORM\Column]
     #[Groups(['order'])]
    #[ORM\JoinColumn(nullable: false)]
    private array $status = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
     #[Groups(['order'])]
    private ?\DateTimeInterface $dateDelivery = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
     #[Groups(['order'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(targetEntity: ServiceItem::class, mappedBy: 'orders')]
    private Collection $servicesItems;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'orderEntity')]
    private Collection $payments;


    public function __construct()
    {
        $this->servicesItems = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function setServiceId(int $serviceId): static
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

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

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setStatus(array $status): static
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServiceItems(): Collection
    {
        return $this->servicesItems;
    }

    public function addServiceItems(ServiceItem $servicesItem): static
    {
        if (!$this->servicesItems->contains($servicesItem)) {
            $this->servicesItems->add($servicesItem);
            $servicesItem->setOrders($this);
        }

        return $this;
    }

    public function removeServiceItems(ServiceItem $servicesItem): static
    {
        if ($this->servicesItems->removeElement($servicesItem)) {
            // set the owning side to null (unless already changed)
            if ($servicesItem->getOrder() === $this) {
                $servicesItem->setOrders(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setOrderEntity($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getOrderEntity() === $this) {
                $payment->setOrderEntity(null);
            }
        }

        return $this;
    }
}
