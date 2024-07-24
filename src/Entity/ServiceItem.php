<?php

namespace App\Entity;

use App\Entity\Course;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ServiceItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
// Contraintes
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceItemRepository::class)]

class ServiceItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    // #[Groups(['serviceItem'])]
    #[Groups(['cart'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
     #[Groups(['serviceItem'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
     #[Groups(['serviceItem'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank]
     #[Groups(['serviceItem'])]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\NotBlank]
     #[Groups(['serviceItem'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
     #[Groups(['serviceItem'])]
    private ?\DateTimeInterface $createDate = null;

    #[ORM\Column(length: 255, nullable: false)]
     #[Groups(['serviceItem'])]
    private ?string $picture = null;

    /**
     * Undocumented Course
     *
     * @var Course|null
     */
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'serviceItems')]
    #[ORM\JoinColumn(nullable: false)]
     #[Groups(['serviceItem'])]
    private ?Course $course = null;
    /**
     * Undocumented User
     *
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'serviceitems')]
    #[ORM\JoinColumn(nullable: false)]
     #[Groups(['serviceItem'])]
    private ?User $user = null;
    /**
     * Undocumented Order
     *
     * @var Order|null
     */
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'serviceitems')]
     #[Groups(['serviceItem'])]
    private ?Order $order = null;



    public function __construct()
    {
        // $this->course = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Undocumented Course
     *
     * @return Course|null
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }
    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }


    /**
     * Undocumented User
     *
     * @return User|null
     */
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
     * Undocumented Order
     *
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrders(?Order $order): static
    {
        $this->order = $order;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): static
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }







    
    public function __toString()
    {
        return $this->title;
    }
}
