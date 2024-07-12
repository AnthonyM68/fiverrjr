<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
// Contraintes
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private $serviceItems;

    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $nameCategory = null;
    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'category')]
    private Collection $courses;

        /**
     * @return Collection<int, Theme>
     */
    #[ORM\ManyToOne(targetEntity: Theme::class, inversedBy: 'categories')]

    #[ORM\JoinColumn(nullable: false)]
    private ?Theme $theme = null;



    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

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
     * @return Collection<int, Theme>
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
     * @return Collection<int, ServiceItem>
     */
    public function getServiceItems(): Collection
    {
        return $this->serviceItems;
    }




    public function __toString()
    {
        return $this->nameCategory;
    }
}
