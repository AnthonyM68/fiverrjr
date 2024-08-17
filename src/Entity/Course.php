<?php

namespace App\Entity;

use App\Entity\ServiceItem;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CourseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CourseRepository::class)]

class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    #[Groups(['course' , 'serviceItem'])]
    private ?int $id = null;

     
    #[ORM\Column(length: 100)]
    #[Groups(['serviceItem'])]
    #[Assert\NotBlank(message: "Choisissez une sous-catégorie")]
    private ?string $nameCourse = null;

    /**
     * ServiceItem
     *
     * @var Collection<int
     */
     #[Groups(['serviceItem'])]
    #[ORM\OneToMany(targetEntity: ServiceItem::class, mappedBy: 'course')]
    private Collection $serviceItems;




    /**
     * Undocumented variable
     *
     * @var Category|null
     */
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'courses')]
     #[Groups(['serviceItem'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;



    public function __construct()
    {
        $this->serviceItems = new ArrayCollection();
    }

    /**
     * Course
     *
     * @return integer|null
     */
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




    /**
     * @return Collection<int, ServiceItem>
     */
    public function getServiceItems(): ?Collection
    {
        return $this->serviceItems;
    }
    public function addServiceItem(ServiceItem $serviceItem): self
    {
        if (!$this->serviceItems->contains($serviceItem)) {
            $this->serviceItems[] = $serviceItem;
            $serviceItem->setCourse($this);
        }

        return $this;
    }

    public function removeServiceItem(ServiceItem $serviceItem): self
    {
        if ($this->serviceItems->removeElement($serviceItem)) {
            // set the owning side to null (unless already changed)
            if ($serviceItem->getCourse() === $this) {
                $serviceItem->setCourse(null);
            }
        }

        return $this;
    }



    /**
     * Undocumented function
     *
     * @return Category|null
     */
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
