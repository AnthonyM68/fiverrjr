<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
     #[Groups(['serviceItem'])]
    private ?int $id = null;


    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
     #[Groups(['serviceItem'])]
    private ?string $nameTheme = null;


    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'theme')]
     #[Groups(['serviceItem'])]
    private Collection $categories;



    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * Theme
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameTheme(): ?string
    {
        return $this->nameTheme;
    }

    public function setNameTheme(string $nameTheme): static
    {
        $this->nameTheme = $nameTheme;

        return $this;
    }






    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setTheme($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getTheme() === $this) {
                $category->setTheme(null);
            }
        }

        return $this;
    }











    public function __toString()
    {
        return $this->nameTheme;
    }
}
