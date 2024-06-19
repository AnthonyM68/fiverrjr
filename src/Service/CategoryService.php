<?php 

namespace App\Service;

use App\Entity\Category;
use App\Entity\Theme;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get categories by theme.
     *
     * @param Theme $theme
     * @return Category[]
     */
    public function getCategoriesByTheme(Theme $theme): array
    {
        return $this->entityManager
            ->getRepository(Category::class)
            ->findBy(['theme' => $theme]);
    }
}
