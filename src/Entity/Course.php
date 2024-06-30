<?php

namespace App\Entity;

use App\Entity\Service;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CourseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $nameCourse = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'course')]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): ?Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setCourse($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getCourse() === $this) {
                $service->setCourse(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCourse(): ?string
    {
        return $this->nameCourse;
    }

    public function setNameCourse(string $nameCourse): static
    {
        $this->nameCourse = $nameCourse;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function __toString()
    {
        return $this->nameCourse;
    }
}
