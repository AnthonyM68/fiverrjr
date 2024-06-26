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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ServiceController extends AbstractController
{
    /**
     * SERVICES
     *
     * @param ServiceRepository $serviceRepository
     * @return Response
     */
    #[Route('/service', name: 'list_services')]
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
    // Restreint l'accès aux utilisateurs authentifiés
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editService(?Service $service = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si le service n'existe pas, crée un nouveau service
        if (!$service) {
            $service = new Service();
        }
        // Variable pour stocker les erreurs de validation
        $errors = null;
        // Crée et gère le formulaire pour le service
        $form = $this->createForm(ServiceType::class, $service);
        // Si le formulaire est soumis et valide, persiste et sauvegarde le thème
        $form->handleRequest($request);
        // Si le formulaire est soumis
        if ($form->isSubmitted()) {
            // Si le formulaire est valide, persiste et sauvegarde la Category
            if ($form->isValid()) {

                $entityManager->persist($service);
                $entityManager->flush();

                // Redirige vers la liste des services après sauvegarde
                return $this->redirectToRoute('list_services');
            } else {
                // Récupère les erreurs de validation
                $errors = $form->getErrors(true);
            }
        }

        // Rend la vue avec le formulaire
        return $this->render('service/index.html.twig', [
            'title_page' => 'Services',
            'formAddService' => $form->createView(),
            'errors' => $errors
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
    // #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]  // Restreint l'accès aux utilisateurs authentifiés
    public function editTheme(?Theme $theme = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si le thème n'existe pas, crée un nouveau thème
        if (!$theme) {
            $theme = new Theme();
        }
        // Variable pour stocker les erreurs de validation
        $errors = null;
        // Crée et gère le formulaire pour le thème
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);
        // Si le formulaire est soumis et valide, persiste et sauvegarde le thème
        // Si le formulaire est soumis
        if ($form->isSubmitted()) {
            // Si le formulaire est valide, persiste et sauvegarde le thème
            if ($form->isValid()) {
                $entityManager->persist($theme);
                $entityManager->flush();
                // Redirige vers la liste des thèmes après sauvegarde
                return $this->redirectToRoute('list_themes');
            } else {
                // Récupère les erreurs de validation
                $errors = $form->getErrors(true);
            }
        }
        // Rend la vue avec le formulaire
        return $this->render('theme/index.html.twig', [
            'title_page' => 'Thèmes',
            'theme_id' => $theme->getId(),
            'formAddTheme' => $form->createView(),
            'errors' => $errors
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

    #[Route('/category/new', name: 'new_category')]
    #[Route('/category/{id}/edit', name: 'edit_category')]
    // Restreint l'accès aux utilisateurs authentifiés
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editCategory(?Category $category = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si la catégorie n'existe pas, crée une nouvelle catégorie
        if (!$category) {
            $category = new Category();
        }
        // Variable pour stocker les erreurs de validation
        $errors = null;
        // Crée et gère le formulaire pour la catégorie
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        // Si le formulaire est soumis et valide, persiste et sauvegarde le thème
        // Si le formulaire est soumis
        if ($form->isSubmitted()) {
            // Si le formulaire est valide, persiste et sauvegarde la Category
            if ($form->isValid()) {
                $entityManager->persist($category);
                $entityManager->flush();
                // Redirige vers la liste des thèmes après sauvegarde
                return $this->redirectToRoute('list_categories');
            } else {
                // Récupère les erreurs de validation
                $errors = $form->getErrors(true);
            }
        }

        // Rend la vue avec le formulaire
        return $this->render('category/index.html.twig', [
            'title_page' => 'Catégories',
            'category_id' => $category->getId(),
            'formAddCategory' => $form->createView(),
            'errors' => $errors
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
    #[IsGranted('IS_AUTHENTICATED_FULLY')]  // Restreint l'accès aux utilisateurs authentifiés
    public function editCourse(?Course $course = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Si le cours n'existe pas, crée un nouveau cours
        if (!$course) {
            $course = new Course();
        }
        // Variable pour stocker les erreurs de validation
        $errors = null;;
        // Crée et gère le formulaire pour le cours
        $form = $this->createForm(CourseType::class, $course);
        // Si le formulaire est soumis et valide, persiste et sauvegarde le thème
        $form->handleRequest($request);
        // Si le formulaire est soumis
        if ($form->isSubmitted()) {
            // Si le formulaire est valide, persiste et sauvegarde la Sous-catégories
            if ($form->isValid()) {
                $entityManager->persist($course);
                $entityManager->flush();
                // Redirige vers la liste des thèmes après sauvegarde
                return $this->redirectToRoute('list_courses');
            } else {
                // Récupère les erreurs de validation
                $errors = $form->getErrors(true);
            }
        }

        // Rend la vue avec le formulaire
        return $this->render('course/index.html.twig', [
            'title_page' => 'Sous-catégories',
            'course_id' => $course->getId(),
            'formAddCourse' => $form->createView(),
            'errors' => $errors
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
