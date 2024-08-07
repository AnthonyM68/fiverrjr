<?php

namespace App\Controller;

// Importation des classes nécessaires
use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Course;
use App\Form\ThemeType;
use App\Entity\Category;
use App\Form\CourseType;
use App\Form\CategoryType;
use App\Entity\ServiceItem;
use Psr\Log\LoggerInterface;
use App\Form\ServiceItemType;
use App\Service\ImageService;
use App\Repository\ThemeRepository;
use App\Repository\CourseRepository;
use Symfony\Component\Form\FormError;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ServiceItemRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Cookie;
// Depérécier
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
// pour générer des token
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
// pour récupérer l'utilisateur courent plutôt que security (deprecier)
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ServiceItemController extends AbstractController
{
    private $logger;
    private $entityManager;
    private $imageService;
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ImageService $imageService,
        TokenStorageInterface $tokenStorage


    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->imageService = $imageService;
        $this->tokenStorage = $tokenStorage;
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
     * Recherche les service d'un utilisateur depuis la page profil
     *
     * @param ServiceItemRepository $serviceItemRepository
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     */
    #[Route('/fetch/get_service', name: 'services_by_user_id', methods: ['GET'])]
    public function getServiceByIdUser(
        ServiceItemRepository $serviceItemRepository,
        CsrfTokenManagerInterface $csrfTokenManager

    ): JsonResponse {
        $user = $this->tokenStorage->getToken()->getUser();
        // on vérifie que user est une instance
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_UNAUTHORIZED);
        }
        // récupérer l'ID de l'user
        $userId = $user->getId();
        // récupérer les services de l'user
        $services = $serviceItemRepository->findBy(['user' => $userId]);
        // format les données pour la réponse JSON
        $data = [];
        foreach ($services as $service) {
            $csrfToken = $csrfTokenManager->getToken('token_' . $service->getId())->getValue();

            $data[] = [
                'id' => $service->getId(), // permet de passer l'argument à javascript
                'title' => $service->getTitle(),
                'csrf_token' => $csrfToken // on inclus un token à chaque service
            ];
        }
        // retourner les données en JSON
        return new JsonResponse($data);
    }
    /**
     *  Génére le formulaire d'édition d'un service pré-remplis
     *
     * @param Request $request
     * @param ServiceItemRepository $serviceItemRepository
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     */
    #[Route('/fetch/service/form/generate', name: 'service_form_generate', methods: ['POST'])]
    public function getGenerateServiceForm(Request $request, ServiceItemRepository $serviceItemRepository, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        // on décode le contenu de la requête
        $body = json_decode($request->getContent(), true);
        
        $this->logger->info('service_form_generate', ['json_decode' => $body]);
        // On récupère l'id du service envoyer par javascript 
        $id = $body['serviceId'];
        // on recherche les données du service correspondant
        $service = $serviceItemRepository->find($id);
        // si le service n'est pas trouvé
        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        // Validation du token CSRF
        // on récupère le token envoyer par javascript
        $csrfToken = new CsrfToken('token_' . $id, $body['_token']);
        // on vérfie le token
        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            // s'il n'est pas valide
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }
        $this->imageService->setPictureUrl($service);

        // on créer le formulaire avec une action personnalisée
        $formAddService = $this->createForm(ServiceItemType::class, $service, [
            // permet la redirection du formulaire par javascript 
            'action' => $this->generateUrl('service_form_update'),
            'attr' => [
                // Permet l'interception du formulaire par javascript Service.js
                'id' => 'serviceForm',
                'data-service-id' => $service->getId(),
            ]
        ]);

        $formAddService->handleRequest($request);
        // on prepare le formulaire en HTML
        $formHtml = $this->renderView('itemService/form/form.html.twig', [
            'serviceId' => $id,
            'errorsFormService' => $formAddService->getErrors(true),
            'formAddService' => $formAddService->createView(),

        ]);
        // on rend le formulaire par la réponse JSON 
        return new JsonResponse(['formHtml' => $formHtml], Response::HTTP_OK);
    }

    /**
     * Mise à jour d'un service depuis AJAX et la page profil pour développeur
     *
     * @param Request $request
     * @param ServiceItemRepository $serviceItemRepository
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     */
    #[Route('/fetch/service/form/update', name: 'service_form_update', methods: ['POST'])]
    public function updateServiceForm(Request $request, ServiceItemRepository $serviceItemRepository): JsonResponse
    {
        // Récupération des données du formulaire
        $formData = $request->request->all();
        $this->logger->info('service_form_update', ['json_decode' => $formData]);
        // // on récupère l'id du service depuis javascript
        $serviceId = $formData['service_id'];
        // Rechercher le service correspondant
        $service = $serviceItemRepository->find($serviceId);

        if (!$service instanceof ServiceItem) {
            return new JsonResponse(['error' => 'Service non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // Créer et manipuler le formulaire
        $formService = $this->createForm(ServiceItemType::class, $service);
        $formService->handleRequest($request);

        if ($formService->isSubmitted()) {
            if ($formService->isValid()) {
                // récupérer l'objet file
                $file = $formService->get('picture')->getData();
                // si un fichier est téléchargé, traiter le fichier
                // si $file est bien une instance de UploadedFile ( ServiceItemType )
                if ($file instanceof UploadedFile) {
                    try {
                        // obtenir le nom du fichier de l'image de profil de l'utilisateur
                        $originalFilename = $service->getPicture();
                        // On supprime l'image actuelle
                        $this->imageService->deleteImage($originalFilename, 'SERVICE');
                        // On déplace la nouvelle et récupère son nom et extention
                        $fileName = $this->imageService->uploadImage($file, 'SERVICE');
                        // On set au service le nouveau nom de fichier
                        $service->setPicture($fileName);
                        // on génére l'url de l'image pour l'affichage
                        $this->imageService->setPictureUrl($service);
                    } catch (\Exception $e) {
                        return new JsonResponse(['success' => false, 'errors' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
                    }
                }
                // Si le formulaire est valide, persister les modifications
                $this->entityManager->persist($service);
                $this->entityManager->flush();

                try {
                    $this->entityManager->remove($service);
                    $this->entityManager->flush();
                    return new JsonResponse(['success' => true, 'message' => 'Le service a bien été mis à jour'], Response::HTTP_OK);
                } catch (\Exception $e) {
                    return new JsonResponse(['error' => 'Failed to delete service'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }


                
            } else {
                // Si le formulaire n'est pas valide, récupérer les erreurs par champ
                foreach ($formService->all() as $fieldName => $field) {
                    $fieldErrors = [];
                    foreach ($field->getErrors(true) as $error) {
                        $fieldErrors[] = $error->getMessage();
                    }
                    if (!empty($fieldErrors)) {
                        $errors[$fieldName] = $fieldErrors;
                    }
                }

                return new JsonResponse(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
            }
        }
        return new JsonResponse(['success' => false, 'errors' => ['form' => 'Formulaire non soumis']], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param ServiceItemRepository $serviceItemRepository
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     */
    #[Route('/fetch/service/delete', name: 'delete_service', methods: ['DELETE'])]
    public function deleteServiceItem(
        Request $request,
        ServiceItemRepository $serviceItemRepository,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse {
        // on décode le contenu de la réponse
        $body = json_decode($request->getContent(), true);
        $this->logger->info('delete_service', ['json_decode' => $body]);
        // // on récupère l'id du service depuis javascript
        $serviceId = $body['serviceId'];
        // // on récupère le token depuis javascript
        $csrfToken = new CsrfToken('token_' . $serviceId, $body['_token']);
        // // on vérifie que le token soit valide
        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }
        // on recherche le service corespondant
        $service = $serviceItemRepository->find($serviceId);

        if (!$service instanceof ServiceItem) {
            return new JsonResponse(['error' => 'Service non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->entityManager->remove($service);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Service deleted successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete service'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * SERVICES
     * Affiche la liste des services triés par date ascendant
     *
     * @param ServiceItemRepository $ServiceItemRepository
     * @return Response
     */
    #[Route('/services', name: 'list_services')]
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
    #[Route('/services/user/{id}', name: 'list_services_by_userID')]
    public function listServiceByUserID(ServiceItemRepository $ServiceItemRepository, int $id): Response
    {
        // Récupère tous les service triés par date
        $services = $ServiceItemRepository->findByUserId($id);
        // Rend la vue avec les services récupérés
        return $this->render('itemService/index.html.twig', [
            'title_page' => 'Liste des Services',
            'services' => $services
        ]);
    }
    /**
     * Créer un nouveau service ou éditer un existant
     */
    #[Route('/service/new', name: 'new_service')]
    #[Route('/service/edit/{id}', name: 'edit_service', methods: ['GET', 'POST'])]
    // restreint l'accès aux utilisateurs authentifiés
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    // on restreint cette route au client du site (pour l'interface plein écran simple)

    // #[IsGranted('ROLE_CLIENT')]

    public function editService(
        EntityManagerInterface $entityManager,
        Request $request,
        ?ServiceItem $service = null

    ): Response {
        $title_page = 'Modification du service';
        // Si le service n'existe pas, crée un nouveau service
        if (!$service) {
            $service = new ServiceItem();
            $title_page = 'Nouveau service';
        }
        // On vérifie que service est bien une instance
        if (!$service instanceof ServiceItem) {
            return new JsonResponse(['error' => 'Service non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // on passe l'id du service au formulaire
        $formService = $this->createForm(ServiceItemType::class, $service, [
            'attr' => [
                'id' => 'serviceForm',
                'data-serviceid' => $service->getId(),
            ]
        ]);

        $formService->handleRequest($request);
        // Si le formulaire est soumis et valide 
        if ($formService->isSubmitted() && $formService->isValid()) {
            // on recherche la valeur de la sous catégorie
            $course = $formService->get('course')->get('course')->getData() ?? null;
            // si on apas  de résultat dans course
            if (!$course) {
                // si aucun cours n'est sélectionné, ajouter une erreur de validation
                $formService->get('course')->addError(new FormError('Veuillez sélectionner une sous-catégorie.'));

                return $this->render('itemService/index.html.twig', [
                    'title_page' => $title_page,
                    'formAddService' => $formService->createView(),
                    'errors' => $formService->getErrors(true),
                ]);
            }
            // on set la sous-categorie au service
            $service->setCourse($course);
            // récupérer l'objet file
            $file = $formService->get('picture')->getData();
            // si un fichier est téléchargé, traiter le fichier
            // si $file est bien une instance de UploadedFile ( ServiceItemType )
            if ($file instanceof UploadedFile) {
                try {
                    $originalFilename = $service->getPicture();
                    // On supprime l'image actuelle
                    $this->imageService->deleteImage($originalFilename, 'SERVICE');
                    // On déplace la nouvelle et récupère son nom et extention
                    $fileName = $this->imageService->uploadImage($file, 'SERVICE');
                    // On set au service le nouveau nom de fichier
                    $service->setPicture($fileName);
                    // on génére l'url de l'image pour l'affichage
                    $this->imageService->setPictureUrl($service);
                } catch (\Exception $e) {
                    // si une exception est levée, afficher un message flash d'erreur
                    $this->addFlash('error', 'Une erreur s\'est produite lors du traitement de l\'image: ' . $e->getMessage());
                }
            }
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('edit_service', ['id' => $service->getId()]);
        }
        // Pré-remplir les champs non mappés
        if ($service->getCourse()) {
            $formService->get('course')->get('course')->setData($service->getCourse());
        }

        $this->imageService->setPictureUrl($service);

        return $this->render('itemService/index.html.twig', [
            'title_page' => $title_page,
            'formAddService' => $formService->createView(),
        ]);
    }


    /**
     * 
     * Affiche le détail d'un service
     */
    #[Route('/service/detail/{id}', name: 'detail_service', methods: ['GET'])]
    public function detailService(
        Request $request,
        ?ServiceItem $service
    ): Response {
        $this->imageService->setPictureUrl($service);
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
                $this->imageService->setPictureUrl($service);
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

    /**
     * THEMES
     */

    #[Route('/theme', name: 'list_themes', methods: ['GET'])]
    public function listThemes(ThemeRepository $themeRepository, ?Theme $theme = null): Response
    {
        // Récupère tous les thèmes triés par nom
        $themes = $themeRepository->findBy([], ["nameTheme" => "ASC"]);
        // Rend la vue avec les thèmes récupérés
        return $this->render('theme/index.html.twig', [
            'title_page' => 'Liste des Thèmes',
            'themes' => $themes
        ]);
    }

    #[Route('/admin/theme/new', name: 'new_theme', methods: ['GET'])]
    #[Route('/admin/theme/edit/{id}', name: 'edit_theme', methods: ['GET', 'POST'])]

    #[IsGranted('ROLE_ADMIN')]  // Restreint l'accès aux admins
    public function editTheme(EntityManagerInterface $entityManager, Request $request, ?Theme $theme = null): Response
    {
        $name_page = '';
        // si le thème n'existe pas, crée un nouveau thème
        if (!$theme) {
            $name_page = "Nouveau";
            $theme = new Theme();
        } else {
            $name_page = "Editez le";
        }
        // crée et gère le formulaire pour le thème
        $form = $this->createForm(ThemeType::class, $theme);
        $form->handleRequest($request);
        // si le formulaire est soumis
        if ($form->isSubmitted()) {
            // si le formulaire est valide
            if ($form->isValid()) {
                $entityManager->persist($theme);
                $entityManager->flush();
                // Redirige vers la liste des thèmes 
                return $this->redirectToRoute('list_themes');
            }
        }
        // Rend la vue avec le formulaire
        return $this->render('theme/index.html.twig', [
            'title_page' => $name_page . ' ' . 'thème',
            'theme_id' => $theme->getId(),
            'formAddTheme' => $form->createView(),
            'errors' => $form->getErrors(true),
            'themes' => $entityManager->getRepository(Theme::class)->findAll()
        ]);
    }


    #[Route('/theme/detail/{id}', name: 'detail_theme', methods: ['GET'])]
    public function detailTheme(?Theme $theme = null): Response
    {
        // Récupère les détails du thème (toutes les catégories)
        $categories = $theme->getCategories();
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
    #[Route('/category', name: 'list_categories', methods: ['GET'])]
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

    #[Route('/admin/category/new', name: 'new_category', methods: ['GET'])]
    #[Route('/admin/category/edit/{id}', name: 'edit_category', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]  // Restreint l'accès aux admins
    public function editCategory(EntityManagerInterface $entityManager, Request $request, ?Category $category = null): Response
    {
        $name_page = '';
        // si la catégorie n'existe pas, crée une nouvelle catégorie
        if (!$category) {
            $name_page = "Nouvelle";
            $category = new Category();
        } else {
            $name_page = "Editez la";
        }
        // crée et gère le formulaire pour la catégorie
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        // si le formulaire est soumis
        if ($form->isSubmitted()) {
            // Si le formulaire est valide
            if ($form->isValid()) {
                $entityManager->persist($category);
                $entityManager->flush();
                // Redirige vers la liste des catégories 
                return $this->redirectToRoute('list_categories');
            }
        }

        // Rend la vue avec le formulaire
        return $this->render('category/index.html.twig', [
            'title_page' => $name_page . ' ' . 'catégorie',
            'category_id' => $category->getId(),
            'formAddCategory' => $form->createView(),
            'errors' => $form->getErrors(true),
            'categories' => $entityManager->getRepository(Category::class)->findAll()
        ]);
    }

    #[Route('/category/{id}/detail', name: 'detail_category', methods: ['GET'])]
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
    #[Route('/course', name: 'list_courses', methods: ['GET'])]
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

    /**
     *  Ajout ou edition d'une sous catégorie
     *  Accès réserver ADMIN
     */
    #[Route('/admin/course/new', name: 'new_course', methods: ['GET'])]
    #[Route('/admin/course/edit/{id}', name: 'edit_course', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editCourse(EntityManagerInterface $entityManager, Request $request, ?Course $course = null): Response
    {
        // Si le cours n'existe pas, crée un nouveau cours
        if (!$course) {
            $course = new Course();
        }
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
            }
        }

        // Rend la vue avec le formulaire
        return $this->render('course/index.html.twig', [
            'title_page' => 'Sous-catégories',
            'course_id' => $course->getId(),
            'formAddCourse' => $form->createView(),
            'errors' => $form->getErrors(true),
            'courses' => $entityManager->getRepository(Course::class)->findAll()
        ]);
    }

    #[Route('/course/detail/{page}/{id}', name: 'detail_course')]
    public function detailCourse(Request $request, PaginatorInterface $paginator, ?Course $course = null): Response
    {
        $limit = 3;
        $services = $course->getServiceItems();

        $page = $request->get('page');

        foreach ($services as $service) {
            $this->imageService->setPictureUrl($service);
        }

        $pagination = $paginator->paginate(
            $services,
            $page,
            $limit
        );
        // Rend la vue avec les détails du cours
        return $this->render('course/index.html.twig', [
            'services' => $pagination,
            'pagination' => $pagination,
            'title_page' => $course->getNameCourse(),
            'course' => $course->getId(),
        ]);
    }
}
