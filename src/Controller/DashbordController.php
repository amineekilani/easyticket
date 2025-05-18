<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\MatchFootballRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashbordController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        MatchFootballRepository $matchFootballRepository,
        CommandeRepository $commandeRepository
    ): Response {
        // Récupérer les 5 derniers utilisateurs inscrits
        $latestUsers = $userRepository->findBy([], ['dateInscription' => 'DESC'], 5);

        // Récupérer les matchs les plus populaires
        $topMatches = $matchFootballRepository->findTopMatches(5);

        // Récupérer les statistiques de ventes des 7 derniers jours
        $salesStats7 = $commandeRepository->getSalesStatsByDays(7);
        $salesStats30 = $commandeRepository->getSalesStatsByDays(30);
        $salesStats90 = $commandeRepository->getSalesStatsByDays(90);

        // Calculer les statistiques globales
        $totalTickets = 0;
        $totalRevenue = 0;
        foreach ($salesStats30 as $stat) {
            $totalTickets += $stat['tickets'];
            $totalRevenue += $stat['revenue'];
        }

        return $this->render('admin/dashbord.html.twig', [
            'latestUsers' => $latestUsers,
            'topMatches' => $topMatches,
            'salesStats7' => $salesStats7,
            'salesStats30' => $salesStats30,
            'salesStats90' => $salesStats90,
            'totalTickets' => $totalTickets,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}
