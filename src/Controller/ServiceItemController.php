<?php

namespace App\Controller;

// services
use App\Entity\Order;
// Importation des classes nécessaires
use App\Entity\Theme;
use App\Service\Cart;
use App\Entity\Course;
use App\Form\OrderType;
use App\Form\ThemeType;
use App\Entity\Category;
use App\Form\CourseType;
use App\Form\CategoryType;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Form\ServiceItemType;
use App\Service\ImageService;
use App\Repository\OrderRepository;
use App\Repository\ThemeRepository;
use App\Repository\CourseRepository;
use Symfony\Component\Form\FormError;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServiceItemRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceItemController extends AbstractController
{
    private $logger;
    private $entityManager;
    private $imageService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ImageService $imageService

    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->imageService = $imageService;
    }
    #[Route('/api/uploadImage', name: 'upload_img', methods: ['POST'])]
    public function uploadImage(UploadedFile $file, string $role): JsonResponse
    {
        try {
            $filename = $this->imageService->uploadImage($file, $role);
            return new JsonResponse(['filename' => $filename], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/images/{filename}', name: 'get_image', methods: ['GET'])]
    public function getImage(string $filename, string $role): Response
    {
        try {
            $filePath = $this->imageService->getImagePath($filename, $role);

            if (!file_exists($filePath)) {
                throw $this->createNotFoundException('Image not found');
            }

            $mimeType = mime_content_type($filePath);
            return new Response(file_get_contents($filePath), Response::HTTP_OK, ['Content-Type' => $mimeType]);
        } catch (\Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }

    #[Route('/api/deleteImage/{filename}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(string $filename, string $role): JsonResponse
    {
        try {
            $this->imageService->deleteImage($filename, $role);
            return new JsonResponse(['message' => 'Image deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/imageUrl/{filename}/{role}', name: 'generate_image_url', methods: ['GET'])]
    public function generateImageUrl(string $filename, string $role): JsonResponse
    {
        try {
            $url = $this->imageService->generateImageUrl($filename, $role);
            return new JsonResponse(['url' => $url], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * SERVICES
     *
     * @param ServiceItemRepository $ServiceItemRepository
     * @return Response
     */
    #[Route('/serviceItem', name: 'list_services')]
    public function index(ServiceItemRepository $ServiceItemRepository): Response
    {
        // Récupère tous les service triés par date
        $services = $ServiceItemRepository->findBy([], ["createDate" => "ASC"]);
        // Rend la vue avec les services récupérés
        return $this->render('itemService/index.html.twig', [
            'title_page' => 'Liste des Services',
            'services' => $services
        ]);
    }

    #[Route('/serviceItem/new', name: 'new_service')]
    #[Route('/serviceItem/{id}/edit', name: 'edit_service')]
    // Restreint l'accès aux utilisateurs authentifiés
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editService(EntityManagerInterface $entityManager, Request $request, ?ServiceItem $service = null): Response
    {
        // Si le service n'existe pas, crée un nouveau service
        if (!$service) {
            $service = new ServiceItem();
        }

        $form = $this->createForm(ServiceItemType::class, $service);
        $form->handleRequest($request);

        // Si le formulaire est soumis
        if ($form->isSubmitted()) {

            // Si le formulaire est valide, persiste et sauvegarde la Category
            if ($form->isValid()) {

                $course = $form->get('course')->get('course')->getData() ?? null;
                // dd($course);
                // si on a un résultat dans course
                if ($course) {
                    // on set la sous-categorie au service
                    $service->setCourse($course);
                } else {
                    // si aucun cours n'est sélectionné, ajouter une erreur de validation
                    $form->get('course')->addError(new FormError('Veuillez sélectionner une sous-catégorie.'));

                    return $this->render('itemService/index.html.twig', [
                        'title_page' => 'Services',
                        'formAddService' => $form->createView(),
                        'errors' => $form->getErrors(true),
                    ]);
                }

                // $pictureFile = $form->get('picture')->getData();
                // if ($pictureFile) {
                //     $imageUploader->uploadImage($pictureFile, $service->getUser());
                // }
                $entityManager->persist($service);
                $entityManager->flush();
            }
        } else {
            // Pré-remplir les champs non mappés
            if ($service->getCourse()) {
                $form->get('course')->get('course')->setData($service->getCourse());
            }
        }

        // Rend la vue avec le formulaire
        return $this->render('itemService/index.html.twig', [
            'title_page' => 'Services',
            'formAddService' => $form->createView(),
            'errors' => $form->getErrors(true),
            // 'service' => $service
        ]);
    }


    #[Route('/serviceItem/detail/{id}', name: 'detail_service')]
    public function detailService(
        Request $request,
        ?ServiceItem $service
    ): Response {

        // récupére le nom de fichier de l'image de l'utilisateur
        $pictureFilename = $service->getPicture();
        // on utilise le controller pour fournir le chemin absolu de l'image ( config: services.yaml )
        if ($pictureFilename) {
            $pictureUrlResponse = $this->forward('App\Controller\ImageController::generateImageUrl', [
                'filename' => $pictureFilename,
                'role' => 'SERVICE'
            ]);
            $pictureUrl = json_decode($pictureUrlResponse->getContent(), true);

            if ($pictureUrl && is_string($pictureUrl['url'])) {
                $service->setPicture($pictureUrl['url']);
            }
        }

        return $this->render('itemService/index.html.twig', [
            'title_page' => 'Détail:',
            'service' => $service,
        ]);
    }


    #[Route('/service/bestServices', name: 'service_bestServices', methods: ['GET'])]
    public function getBestServices(ServiceItemRepository $serviceItemRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            // PRévoir nouveau critère de recherche (meilleurs)
            $services = $serviceItemRepository->findBy([], ['id' => 'DESC'], 10);
            $this->logger->info('findBy id DESC services', ['count' => count($services)]);

            foreach ($services as $service) {
                $pictureFilename = $service->getPicture();
                $this->logger->info('Processing service', ['service' => $service->getTitle(), 'pictureFilename' => $pictureFilename]);

                if ($pictureFilename) {
                    try {
                        $pictureUrl = $this->imageService->generateImageUrl($pictureFilename, 'SERVICE');
                        $this->logger->info('Generated picture URL', [
                            'service' => $service->getTitle(),
                            'pictureUrl' => $pictureUrl
                        ]);
                        $service->setPicture($pictureUrl);
                    } catch (\Exception $e) {
                        $this->logger->error('Failed to generate picture URL', [
                            'service' => $service->getTitle(),
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }
                }
            }
            $this->logger->info('All services processed', ['services' => $services]);

            $jsonFullCart = $serializer->serialize($services, 'json', ['groups' => 'serviceItem']);
            $data = json_decode($jsonFullCart, true);

            return new JsonResponse(['services' => $data], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Failed to load services', ['error' => $e->getMessage()]);
            return new JsonResponse(['error' => 'Failed to load services.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    #[Route('/service/form/generate/{id}', name: 'service_form_generate', methods: ['POST'])]
    public function getGenerateServiceForm(int $id = null, Request $request, ServiceItemRepository $ServiceItemRepository, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $service = $ServiceItemRepository->find($id);

        // on décode le contenu de la réponse
        $data = json_decode($request->getContent(), true);
        $csrfToken = new CsrfToken('delete_service_' . $id, $data['_token']);


        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }
        $formAddService = $this->createForm(ServiceItemType::class, $service);
        $formAddService->handleRequest($request);

        // Pré-remplir les champs non mappés
        if ($service->getCourse()) {
            $formAddService->get('course')->get('course')->setData($service->getCourse());
        }
        // Rendre le formulaire en HTML
        $formHtml = $this->renderView('itemService/form/form.html.twig', [
            'formAddService' => $formAddService->createView(),
        ]);
        // Retourner la réponse JSON 
        return new JsonResponse(['formHtml' => $formHtml], Response::HTTP_OK);
    }

    #[Route('/serviceItem/delete/{serviceId}', name: 'delete_service', methods: ['DELETE'])]
    public function deleteServiceItem(int $serviceId, Request $request, ServiceItemRepository $serviceItemRepository, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        // on décode le contenu de la réponse
        $data = json_decode($request->getContent(), true);
        $csrfToken = new CsrfToken('delete_service_' . $serviceId, $data['_token']);


        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $service = $serviceItemRepository->find($serviceId);

        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->entityManager->remove($service);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Service deleted successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete service'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/get_service_by_user', name: 'services_by_user_id', methods: ['GET'])]

    public function getServiceByIdUser(
        ServiceItemRepository $serviceItemRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
        Request $request,
        Security $security

    ): JsonResponse {

        $token = $request->getSession();
        $user = $security->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        // vérifie que l'user est une instance de Class User
        if (!$user instanceof \App\Entity\User) {
            return new JsonResponse(['error' => 'User not recognized'], JsonResponse::HTTP_UNAUTHORIZED);
        }
        // récupérer l'ID de l'user
        $userId = $user->getId();
        // récupérer les services de l'user
        $services = $serviceItemRepository->findBy(['user' => $userId]);
        // format les données pour la réponse JSON
        $data = [];
        foreach ($services as $service) {
            $csrfToken = $csrfTokenManager->getToken('delete_service_' . $service->getId())->getValue();

            $data[] = [
                'id' => $service->getId(),
                'title' => $service->getTitle(),
                'csrf_token' => $csrfToken
            ];
        }
        // retourner les données en JSON
        return new JsonResponse($data);
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
            'title_page' => 'Liste des Thèmes',
            'themes' => $themes
        ]);
    }

    #[Route('/theme/new', name: 'new_theme')]
    #[Route('/theme/{id}/edit', name: 'edit_theme')]
    // #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]  // Restreint l'accès aux utilisateurs authentifiés
    public function editTheme(EntityManagerInterface $entityManager, Request $request, ?Theme $theme = null): Response
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
    public function detailTheme(?Theme $theme = null): Response
    {
        // Récupère les détails du thème en fonction de l'ID
        $categories = $theme->getCategories();
        // Rend la vue avec les détails du thème
        return $this->render('theme/index.html.twig', [
            'title_page' => $theme->getNameTheme(),
            'categories' => $categories,
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
            'title_page' => 'Liste des Catégories',
            'categories' => $categories
        ]);
    }

    #[Route('/category/new', name: 'new_category')]
    #[Route('/category/{id}/edit', name: 'edit_category')]
    // Restreint l'accès aux utilisateurs authentifiés
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editCategory(EntityManagerInterface $entityManager, Request $request, ?Category $category = null): Response
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
    public function detailCategory(?Category $category = null): Response
    {
        $courses = $category->getCourses();
        // Rend la vue avec les détails de la catégorie
        return $this->render('category/index.html.twig', [
            'title_page' => $category->getNameCategory(),
            'courses' => $courses,
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

        // Rend la vue avec les course récupérés
        return $this->render('course/index.html.twig', [
            'title_page' => 'Liste des Sous-catégories',
            'courses' => $courses
        ]);
    }

    #[Route('/course/new', name: 'new_course')]
    #[Route('/course/edit/{id}', name: 'edit_course')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]  // Restreint l'accès aux utilisateurs authentifiés
    public function editCourse(EntityManagerInterface $entityManager, Request $request, ?Course $course = null): Response
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
    public function detailCourse(?Course $course = null): Response
    {
        $services = $course->getServiceItems();
        // Rend la vue avec les détails du cours
        return $this->render('course/index.html.twig', [
            'title_page' => $course->getNameCourse(),
            'services' => $services,
        ]);
    }
}
