<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use App\Repository\MatchFootballRepository;
use App\Repository\StadeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CustomerController extends AbstractController
{
    #[Route('/', name: 'app_customer')]
    public function index(): Response
    {
        return $this->render('customer/acceuil.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }
    #[Route('/layout', name: 'app_customer_layout')]
    public function index2(): Response
    {
        return $this->render('customer/modelelayout.html.twig', [
            'controller_name' => 'CustomerController',
        ]);
    }

    #[Route('/matchs', name: 'app_customer_matchs')]
    public function consulterMatchs(
        MatchFootballRepository $matchRepo,
        EquipeRepository $equipeRepo,
        StadeRepository $stadeRepo,
        Request $request
    ): Response {
        $filters = [
            'equipe' => $request->query->get('equipe'),
            'stade' => $request->query->get('stade'),
            'date' => $request->query->get('date'),
        ];

        // Vérifier si AU MOINS UN filtre manuel est actif
        $hasManualFilters = !empty(array_filter($filters));

        if ($hasManualFilters) {
            $matchs = $matchRepo->findWithFilters($filters);
        } else {
            // Appliquer le filtre par défaut "Cette semaine" SEULEMENT si aucun filtre manuel
            $matchs = $matchRepo->findByDateRange('week');
        }

        return $this->render('customer/matchs.html.twig', [
            'matchs' => $matchs,
            'equipes' => $equipeRepo->findAll(),
            'stades' => $stadeRepo->findAll(),
            'filters' => $filters,
            'hasManualFilters' => $hasManualFilters,
            'activeDateTab' => $hasManualFilters ? null : 'week'
        ]);
    }

    #[Route('/matchs/date-filter', name: 'app_matchs_date_filter')]
    public function filterByDate(Request $request, MatchFootballRepository $repo): Response
    {
        $range = $request->query->get('range');
        $matchs = $repo->findByDateRange($range);

        return $this->render('customer/_match_cards.html.twig', [
            'matchs' => $matchs
        ]);
    }

}
