<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarController extends AbstractController
{
    private $entityManager;
    // Constructeur pour injecter l'EntityManager
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/navbar', name: 'app_navbar')]
    public function index(Request $request): Response
    {
        // Création et gestion du formulaire de recherche
        $formTheme = $this->createForm(SearchFormType::class, null, [
            // On change la route par défaut pour la recherche dynamic
            'action' => $this->generateUrl('search_results'),
            'search_table' => 'theme',
            'search_label' => 'Recherchez votre service',
        ]);
        $formTheme->handleRequest($request);
        $results = [];
        $searchTerm = null;
        $submittedFormName = null;
        // Vérification si le formulaire est soumis et valide
        if ($formTheme->isSubmitted() && $formTheme->isValid() && $request->request->get('submitted_form_type') === 'service') {
            // On récupére les données de l'input
            $searchTerm = $formTheme->get('search_term')->getData();
            // Recherche des résultats correspondants au terme de recherche
            $results = $this->entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
            // Si aucun résultat n'est trouvé, on ajoute un indicateur 'empty'
            if (empty($results)) {
                $results['empty'] = true;
            }
            $submittedFormName = 'form_service';
        }
        return $this->render('navbar/index.html.twig', [
            'form_service' => $formTheme->createView(),
            'results' => $results,
            'search_term' => $searchTerm,
            'submitted_form' => $submittedFormName,
        ]);
    }
}
