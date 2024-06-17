<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }

    #[Route('/category', name: 'list_category')]
    public function listCategory(CategoryRepository $CategoryRepository): Response
    {
        // $categories = $CategoryRepository->findBy([], ["name_category" => "ASC"]);
        return $this->render('service/index.html.twig', [
            'view_name' => 'service/index.html.twig'
            // "categories" => $categories
        ]);
    }
    #[Route('/course', name: 'list_course')]
    public function listCourse(CategoryRepository $CategoryRepository): Response
    {
        // $courses = $CategoryRepository->findBy([], ["name_course" => "ASC"]);
        return $this->render('service/index.html.twig', [
            'view_name' => 'service/index.html.twig'
            // "courses" => $courses
        ]);
    }
}
