<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
// Contraintes
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
     #[Groups(['serviceItem'])]
    private ?int $id = null;

    private $services;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Choisissez une Catégorie")]
     #[Groups(['serviceItem'])]
    private ?string $nameCategory = null;

    
    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'category')]
     #[Groups(['serviceItem'])]
    private Collection $courses;


/**
 * Undocumented variable
 *
 * @var Theme|null
 */
    #[ORM\ManyToOne(targetEntity: Theme::class, inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
     #[Groups(['serviceItem'])]
    private ?Theme $theme = null;



    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    /**
     * Category
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNameCategory(): ?string
    {
        return $this->nameCategory;
    }
    public function setNameCategory(string $nameCategory): static
    {
        $this->nameCategory = $nameCategory;

        return $this;
    }




    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setCategory($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getCategory() === $this) {
                $course->setCategory(null);
            }
        }

        return $this;
    }





    /**
     * Undocumented function
     *
     * @return Theme|null
     */
    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): static
    {
        $this->theme = $theme;

        return $this;
    }





    public function __toString()
    {
        return $this->nameCategory;
    }
}
