<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Service;
use App\Form\ThemeType;
use App\Entity\Category;
use App\Form\CourseType;
use App\Form\AnnonceType;
use App\Form\ServiceType;
use App\Form\CategoryType;
use App\Repository\ThemeRepository;
use App\Repository\CourseRepository;
use App\Repository\ServiceRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/create', name: 'app_meetup_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $meetup = new AnnonceType();
        $form = $this->createForm(AnnonceType::class, $meetup, ['action' => $this->generateUrl('app_meetup_create')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // ... save the meetup, redirect etc.
        }

        return $this->render('service/index_test.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/test/new', name: 'new_test')]
    public function editTest(EntityManagerInterface $entityManager, Request $request): Response
    {

        $course = new Course(); // Créez une nouvelle instance de Course

        $form = $this->createForm(AnnonceType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('list_service');
        }

        return $this->render('service/index_test.html.twig', [
            'controller_name' => 'ServiceController',
            'formAddService' => $form->createView()
        ]);
    }


    #[Route('/service/new', name: 'new_service')]
    #[Route('/service/edit/{id}', name: 'edit_service')]
    public function editService(?Service $service = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$service) {
            $service = new Service();
        }
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('list_courses');
        }

        return $this->render('service/index.html.twig', [
            'controller_name' => 'CourseController',
            'course_id' => $service->getId(),
            'formAddService' => $form->createView()
        ]);
    }
    #[Route('/theme', name: 'list_themes')]
    public function listThemes(ThemeRepository $categoryRepository): Response
    {
        $themes = $categoryRepository->findBy([], ["nameTheme" => "ASC"]);
        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ServiceController',
            'themes' => $themes
        ]);
    }

    #[Route('/theme/new', name: 'new_theme')]
    #[Route('/theme/{id}/edit', name: 'edit_theme')]
    public function editTheme(?Theme $theme = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$theme) {
            $theme = new Theme();
        }

        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $theme = $form->getData();
            // prepare PDO
            $entityManager->persist($theme);
            // execute PDO
            $entityManager->flush();

            return $this->redirectToRoute('list_themes');
        }
        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ServiceController',
            'theme_id' => $theme->getId(),
            'formAddTheme' => $form
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
    #[Route('/categories/{id}', name: 'get_courses_by_category', methods: ['GET'])]
    public function getCoursesByCategory(Category $category): Response
    {
        // Récupérez les cours associés à la catégorie
        $courses = $category->getCourses();

        // Générez une réponse JSON avec les cours
        $response = new JsonResponse($courses);

        return $response;
    }














    #[Route('/category/new', name: 'new_category')]
    #[Route('/category/{id}/edit', name: 'edit_category')]
    public function editCategory(?Category $category = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$category) {
            $category = new Category();
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();
            // prepare PDO
            $entityManager->persist($category);
            // execute PDO
            $entityManager->flush();

            return $this->redirectToRoute('list_categories');
        }
        return $this->render('category/index.html.twig', [
            'controller_name' => 'ServiceController',
            'category_id' => $category->getId(),
            'formAddCategory' => $form
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

    #[Route('/course/new', name: 'new_course')]
    #[Route('/course/edit/{id}', name: 'edit_course')]
    public function editCourse(?Course $course = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$course) {
            $course = new Course();
        }
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('list_courses');
        }

        return $this->render('course/index.html.twig', [
            'controller_name' => 'CourseController',
            'course_id' => $course->getId(),
            'formAddCourse' => $form->createView()
        ]);
    }
}
