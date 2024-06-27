<?php

namespace App\Controller;
// Importation des classes nécessaires
use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Service;
use App\Entity\Category;
use App\Form\SearchFormType;
use App\Form\ServiceSearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/search", name: "search")]
    public function search(Request $request): Response
    {
        // Création des trois instances de formulaire pour chaque type de recherche
        $formTheme = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'theme',
            'search_label' => 'Par Thême:',
        ]);
        $formCategory = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'category',
            'search_label' => 'Par Catégorie:',
        ]);
        $formCourse = $this->createForm(SearchFormType::class, null, [
            'search_table' => 'course',
            'search_label' => 'Par Sous-Catégorie:',
        ]);
        // Gestion de la soumission des formulaires
        $formTheme->handleRequest($request);
        $formCategory->handleRequest($request);
        $formCourse->handleRequest($request);

        // Déclaration des résultats de recherche
        $results = [];
        $submittedFormName = null;
        // Traitement des soumissions de formulaire et exécution des requêtes
        // Chaque formulaire est vérifier par l'attribut value de l'input hidden pour éviter la confusion des formulaires
        if ($formTheme->isSubmitted() && $formTheme->isValid() && $request->request->get('submitted_form_type') === 'theme') {
            $searchTerm = $formTheme->get('search_term')->getData();
            $results['theme'] = $this->entityManager->getRepository(Theme::class)->findByTerm($searchTerm);
            if(empty($results['theme'])) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_theme';
        }
        if ($formCategory->isSubmitted() && $formCategory->isValid() && $request->request->get('submitted_form_type') === 'category') {
            $searchTerm = $formCategory->get('search_term')->getData();
            $results['category'] = $this->entityManager->getRepository(Category::class)->findByTerm($searchTerm);
            if(empty($results['category'])) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_category';
        }
        if ($formCourse->isSubmitted() && $formCourse->isValid() && $request->request->get('submitted_form_type') === 'course') {
            $searchTerm = $formCourse->get('search_term')->getData();
            $results['course'] = $this->entityManager->getRepository(Course::class)->findByTerm($searchTerm);
            if(empty($results['course'])) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_course';
        }
         // Gestion du formulaire personnalisé
         if ($request->isMethod('POST') && $request->request->has('service_search_term')) {
            $searchTerm = $request->request->get('custom_search_term');
            
            $results['service'] = $this->entityManager->getRepository(Service::class)->findByTerm($searchTerm);
          
            if (empty($results['service'])) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_service';
        }
        // Comptage des enregistrements
        $themeCount = $this->entityManager->getRepository(Theme::class)->countAll();
        $categoryCount = $this->entityManager->getRepository(Category::class)->countAll();
        $courseCount = $this->entityManager->getRepository(Course::class)->countAll();
        $serviceCount = $this->entityManager->getRepository(Service::class)->countAll();

        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
            'title_page' => 'Résultats de la recherche',
            'form_theme' => $formTheme->createView(),
            'form_category' => $formCategory->createView(),
            'form_course' => $formCourse->createView(),
            'results' => $results,
            'submitted_form' => $submittedFormName,
            'theme_count' => $themeCount,
            'category_count' => $categoryCount,
            'course_count' => $courseCount,
            'service_count' => $serviceCount,
            'errors' => $formTheme->getErrors(true), // Ajoutez les erreurs ici
        ]);
    }
}
