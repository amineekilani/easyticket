<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\MatchFootballRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashbordController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(UserRepository $userRepository, MatchFootballRepository $matchFootballRepository): Response
    {
        // Récupérer les 5 derniers utilisateurs inscrits
        $latestUsers = $userRepository->findBy([], ['dateInscription' => 'DESC'], 5);

        // Récupérer les matchs les plus populaires
        $topMatches = $matchFootballRepository->findTopMatches(5);

        return $this->render('admin/dashbord.html.twig', [
            'latestUsers' => $latestUsers,
            'topMatches' => $topMatches,
        ]);
    }
}
