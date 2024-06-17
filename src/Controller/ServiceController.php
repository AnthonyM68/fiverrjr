<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\ServiceRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'list_service')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findAll();
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
            'services' => $services
        ]);
    }
    #[Route('/category', name: 'list_categories')]
    public function listCategories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ["nameCategory" => "ASC"]);
        return $this->render('category/index.html.twig', [
            'controller_name' => 'ServiceController',
            'categories' => $categories
        ]);
    }
    #[Route('/course', name: 'list_courses')]
    public function listCourses(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findBy([], ["nameCourse" => "ASC"]);
        return $this->render('course/index.html.twig', [
            'controller_name' => 'ServiceController',
            'courses' => $courses
        ]);
    }

}
