<?php

namespace App\Service;

use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;

class NavBarService
{
    private $themeRepository;
    private $categoryRepository;
    private $courseRepository;

    public function __construct(ThemeRepository $themeRepository, CategoryRepository $categoryRepository, CourseRepository $courseRepository)
    {
        // On injecte les instances des repositories nécessaires
        $this->themeRepository = $themeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->courseRepository = $courseRepository;
    }
    public function getNavBarData()
    {
        // On recherche tous les enregistrements Theme
        $themes = $this->themeRepository->findAll();
        $data = [];
        // Pour chaque Theme on recherche les Category en relation
        foreach ($themes as $theme) {
            $categories = $this->categoryRepository->findBy(['theme' => $theme->getId()]);
            $categoryData = [];
            // Pour chacune de ces Category, nous recherchons les Course en relation
            foreach ($categories as $category) {
                $courses = $this->courseRepository->findBy(['category' => $category->getId()]);
                $courseData = [];
                // On remplis le tableau de resultat des Course
                foreach ($courses as $course) {
                    $courseData[] = [
                        'id' => $course->getId(),
                        'name' => $course->getNameCourse(),
                    ];
                }
                // On remplis le tableau de resultat, par des Category
                $categoryData[] = [
                    'id' => $category->getId(),
                    'name' => $category->getNameCategory(),
                    'courses' => $courseData,
                ];
            }
            // On remplis le tableau de resultat, par des Theme
            $data[] = [
                'id' => $theme->getId(),
                'name' => $theme->getNameTheme(),
                'categories' => $categoryData,
            ];
        }
        return $data;
    }
}
