<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\Service;
use App\Entity\Theme;
use App\Entity\Category;
use App\Entity\Course;
use App\Form\ServiceType;
use App\Form\ThemeType;
use App\Form\CategoryType;
use App\Form\CourseType;
use App\Repository\ServiceRepository;
use App\Repository\ThemeRepository;
use App\Repository\CategoryRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
// Importation correcte pour IsGranted
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ServiceController extends AbstractController
{
    /**
     * SERVICES
     *
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    #[Route('/service', name: 'list_service')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        // Récupère tous les services de la base de données
        $services = $serviceRepository->findAll();

        // Rend la vue avec les services récupérés
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
            'services' => $services
        ]);
    }

    #[Route('/service/new', name: 'new_service')]
    #[Route('/service/edit/{id}', name: 'edit_service')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]  // Restreint l'accès aux utilisateurs authentifiés

    public function editService(?Service $service = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si le service n'existe pas, crée un nouveau service
        if (!$service) {
            $service = new Service();
        }

        // Crée et gère le formulaire pour le service
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, persiste et sauvegarde le service
        if ($form->isSubmitted() && $form->isValid()) {
            $subFormData = $form->get('course')->getData();
            $category = $subFormData['course'] ?? null;

            if ($category) {
                $service->setCourse($category);
            }
            $entityManager->persist($service);
            $entityManager->flush();

            // Redirige vers la liste des services après sauvegarde
            return $this->redirectToRoute('list_service');
        }

        // Rend la vue avec le formulaire
        return $this->render('service/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /* Détails d'un service*/
    #[Route('/service/{id}/detail', name: 'detail_service')]
    public function detailService(Service $service, Request $request, ServiceRepository $serviceRepository): Response
    {
        $service = $serviceRepository->findBy(["id" => $service->getId()]);
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
            'view_name' => 'formation/detail.html.twig',
            "service" => $service
        ]);
    }
    /**
     * THEMES
     */

    #[Route('/theme', name: 'list_themes')]
    public function listThemes(ThemeRepository $themeRepository): Response
    {
        // Récupère tous les thèmes triés par nom
        $themes = $themeRepository->findBy([], ["nameTheme" => "ASC"]);
        
        // Rend la vue avec les thèmes récupérés
        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ServiceController',
            'themes' => $themes
        ]);
    }

    #[Route('/theme/new', name: 'new_theme')]
    #[Route('/theme/{id}/edit', name: 'edit_theme')]
    public function editTheme(?Theme $theme = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si le thème n'existe pas, crée un nouveau thème
        if (!$theme) {
            $theme = new Theme();
        }

        // Crée et gère le formulaire pour le thème
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, persiste et sauvegarde le thème
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($theme);
            $entityManager->flush();

            // Redirige vers la liste des thèmes après sauvegarde
            return $this->redirectToRoute('list_themes');
        }

        // Rend la vue avec le formulaire
        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ServiceController',
            'theme_id' => $theme->getId(),
            'formAddTheme' => $form
        ]);
    }

    #[Route('/theme/{id}/detail', name: 'detail_theme')]
    public function detailTheme(?Theme $theme = null, ThemeRepository $themeRepository, Request $request): Response
    {
        // Récupère les détails du thème en fonction de l'ID
        $theme = $themeRepository->findBy(["id" => $theme->getId()]);

        // Rend la vue avec les détails du thème
        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ServiceController',
            'theme' => $theme,
        ]);
    }

    #[Route('/categories_and_courses_by_theme/{themeId}', name: 'categories_by_theme', methods: ['GET'])]
    public function getCategoriesAndCoursesByTheme(int $themeId, CategoryRepository $categoryRepository): JsonResponse
    {
        // Récupère les catégories associées à un thème
        $categories = $categoryRepository->findBy(['theme' => $themeId]);
        $data = [];

        // Pour chaque catégorie, récupère les cours associés
        foreach ($categories as $category) {
            $courses = [];
            foreach ($category->getCourses() as $course) {
                $courses[] = [
                    'id' => $course->getId(),
                    'name' => $course->getNameCourse(),
                ];
            }

            // Ajoute les catégories et leurs cours dans un tableau
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getNameCategory(),
                'courses' => $courses,
            ];
        }

        // Retourne les données en JSON
        return new JsonResponse($data);
    }

    #[Route('/categories_by_theme/{themeId}', name: 'categories_by_theme', methods: ['GET'])]
    public function getCategoriesByTheme(int $themeId, CategoryRepository $categoryRepository): JsonResponse
    {
        // Récupère les catégories associées à un thème
        $categories = $categoryRepository->findBy(['theme' => $themeId]);
        $data = [];

        // Pour chaque catégorie, récupère les détails
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getNameCategory(),
            ];
        }

        // Retourne les données en JSON
        return new JsonResponse($data);
    }

    // Affiche toutes les catégories
    #[Route('/category', name: 'list_categories')]
    public function listCategories(CategoryRepository $categoryRepository): Response
    {
        // Récupère toutes les catégories triées par nom
        $categories = $categoryRepository->findBy([], ["nameCategory" => "ASC"]);

        // Rend la vue avec les catégories récupérées
        return $this->render('category/index.html.twig', [
            'controller_name' => 'ServiceController',
            'categories' => $categories
        ]);
    }

    #[Route('/category/new/test', name: 'new_category')]
    #[Route('/category/{id}/edit', name: 'edit_category')]
    public function editCategory(?Category $category = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si la catégorie n'existe pas, crée une nouvelle catégorie
        if (!$category) {
            $category = new Category();
        }

        // Crée et gère le formulaire pour la catégorie
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, persiste et sauvegarde la catégorie
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            // Redirige vers la liste des catégories après sauvegarde
            return $this->redirectToRoute('list_categories');
        }

        // Rend la vue avec le formulaire
        return $this->render('category/index.html.twig', [
            'category_id' => $category->getId(),
            'formAddCategory' => $form
        ]);
    }

    #[Route('/category/{id}/detail', name: 'detail_category')]
    public function detailCategory(?Category $category = null, ThemeRepository $categoryRepository, Request $request): Response
    {
        // Récupère les détails de la catégorie en fonction de l'ID
        $category = $categoryRepository->findBy(["id" => $category->getId()]);

        // Rend la vue avec les détails de la catégorie
        return $this->render('category/index.html.twig', [
            'controller_name' => 'ServiceController',
            'category' => $category,
        ]);
    }

    #[Route('/courses_by_category/{categoryId}', name: 'courses_by_category', methods: ['GET'])]
    public function getCoursesByCategory(int $categoryId, CourseRepository $courseRepository): JsonResponse
    {
        // Récupère les cours associés à une catégorie
        $courses = $courseRepository->findBy(['category' => $categoryId]);
        $data = [];

        // Pour chaque cours, récupère les détails
        foreach ($courses as $course) {
            $data[] = [
                'id' => $course->getId(),
                'name' => $course->getNameCourse(),
            ];
        }

        // Retourne les données en JSON
        return new JsonResponse($data);
    }

    /**
     * COURSES 
     *
     * @param CourseRepository $courseRepository
     * @return Response
     */
    #[Route('/course', name: 'list_courses')]
    public function listCourses(CourseRepository $courseRepository): Response
    {
        // Récupère tous les cours triés par nom
        $courses = $courseRepository->findBy([], ["nameCourse" => "ASC"]);

        // Rend la vue avec les cours récupérés
        return $this->render('course/index.html.twig', [
            'controller_name' => 'ServiceController',
            'courses' => $courses
        ]);
    }

    #[Route('/course/new', name: 'new_course')]
    #[Route('/course/edit/{id}', name: 'edit_course')]
    public function editCourse(?Course $course = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si le cours n'existe pas, crée un nouveau cours
        if (!$course) {
            $course = new Course();
        }

        // Crée et gère le formulaire pour le cours
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, persiste et sauvegarde le cours
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            // Redirige vers la liste des cours après sauvegarde
            return $this->redirectToRoute('list_courses');
        }

        // Rend la vue avec le formulaire
        return $this->render('course/index.html.twig', [
            'controller_name' => 'CourseController',
            'course_id' => $course->getId(),
            'formAddCourse' => $form->createView()
        ]);
    }

    #[Route('/course/{id}/detail', name: 'detail_course')]
    public function detailCourse(?Course $course = null, ThemeRepository $courseRepository, Request $request): Response
    {
        // Récupère les détails du cours en fonction de l'ID
        $course = $courseRepository->findBy(["id" => $course->getId()]);

        // Rend la vue avec les détails du cours
        return $this->render('category/index.html.twig', [
            'controller_name' => 'ServiceController',
            'category' => $course,
        ]);
    }
}
