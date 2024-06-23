<?php

namespace App\Entity;

use App\Entity\Course;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    // /**
    //  * @var Collection<int, course>
    //  */
    // #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'services')]
    // private Collection $course;
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\ManyToOne(inversedBy: 'service')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    private ?Order $orders = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createDate = null;

    public function __construct()
    {
        // $this->course = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }

    // /**
    //  * @return Collection<int, course>
    //  */
    // public function getCourse(): Collection
    // {
    //     return $this->course;
    // }

    // public function addCourse(course $course): static
    // {
    //     if (!$this->course->contains($course)) {
    //         $this->course->add($course);
    //     }

    //     return $this;
    // }

    // public function removeCourse(course $course): static
    // {
    //     $this->course->removeElement($course);

    //     return $this;
    // }

    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): static
    {
        $this->orders = $orders;

        return $this;
    }


    public function __toString()
    {
        return $this->title;
    }
}
