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
        $this->themeRepository = $themeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->courseRepository = $courseRepository;
    }

    public function getNavBarData()
    {
        $themes = $this->themeRepository->findAll();
        $data = [];

        foreach ($themes as $theme) {
            $categories = $this->categoryRepository->findBy(['theme' => $theme->getId()]);
            $categoryData = [];

            foreach ($categories as $category) {
                $courses = $this->courseRepository->findBy(['category' => $category->getId()]);
                $courseData = [];

                foreach ($courses as $course) {
                    $courseData[] = [
                        'id' => $course->getId(),
                        'name' => $course->getNameCourse(),
                    ];
                }

                $categoryData[] = [
                    'id' => $category->getId(),
                    'name' => $category->getNameCategory(),
                    'courses' => $courseData,
                ];
            }

            $data[] = [
                'id' => $theme->getId(),
                'name' => $theme->getNameTheme(),
                'categories' => $categoryData,
            ];
        }
        return $data;
    }
}
