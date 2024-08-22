<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Service\CartService;
use App\Form\SearchFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarController extends AbstractController
{
    #[Route('/navbar', name: 'app_navbar')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        CartService $cart
    ): Response {
        // On vérifie si un panier existe en session
        // $fullCart = $cart->getCart($request);
        // Création et gestion du formulaire de recherche
        // ( SearchFormType )
        // $formTheme = $this->createForm(SearchFormType::class, null, [
        //     // On change la route par défaut pour la recherche dynamic
        //     // 'action' => $this->generateUrl('search_results'),
        //     // Envisager un changement / Axe d'amélioration du moteur de recherche
        //     'search_table' => 'theme',
        //     'search_label' => 'Recherchez votre service',
        // ]);
        // $formTheme->handleRequest($request);
        // initialistation d'un tableau de résultats vide
        // dd($request);
        // $searchTerm = $request->get('search_term')->getData();

        $formServiceDesktop = $this->createForm(SearchFormType::class, null, [
            'id_suffix' => 'desktop', // Identifiant unique pour la version desktop

        ]);
        $formServiceMobile = $this->createForm(SearchFormType::class, null, [
            'id_suffix' => 'mobile', // Identifiant unique pour la version mobile

        ]);
    
        // dd($formServiceDesktop, $formServiceMobile);

        // $results = [];
        // // variable pour contenir le term a rechercher
        // $searchTerm = null;
        // // variable pour contenir le nom du formulaire soumis
        // $submittedFormName = null;
        // // Vérification si le formulaire est soumis et valide
        // if ($formServiceDesktop->isSubmitted() && $formServiceDesktop->isValid()) {
        //     // On récupére les données de l'input
        //     $searchTerm = $formServiceDesktop->get('search_term')->getData();
        //     // Recherche des résultats correspondants au terme de recherche
        //     $results = $entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
        //     // Si aucun résultat n'est trouvé, on ajoute un indicateur 'empty'
        //     if (empty($results)) {
        //         $results['empty'] = true;
        //     }
        //     $submittedFormName = 'form_service';
        // }
        // // Vérification si le formulaire est soumis et valide
        // if ($formServiceMobile->isSubmitted() && $formServiceMobile->isValid()) {
        //     // On récupére les données de l'input
        //     $searchTerm = $formServiceMobile->get('search_term')->getData();
        //     // Recherche des résultats correspondants au terme de recherche
        //     $results = $entityManager->getRepository(Theme::class)->searchByTermAllChilds($searchTerm);
        //     // Si aucun résultat n'est trouvé, on ajoute un indicateur 'empty'
        //     if (empty($results)) {
        //         $results['empty'] = true;
        //     }
        //     $submittedFormName = 'form_service';
        // }

        return $this->render('navbar/index.html.twig', [
            'formServiceDesktop' => $formServiceDesktop->createView(),
            'formServiceMobile' => $formServiceMobile->createView(),
            'page' => '1'
            // 'results' => $results,
            // 'search_term' => $searchTerm,
            // 'submitted_form' => $submittedFormName,
            // 'totalServiceItem' => $fullCart['totalServiceItem']
        ]);
    }
}
