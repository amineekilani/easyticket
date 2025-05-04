<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashbordController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(UserRepository $userRepository): Response
    {
        $latestUsers = $userRepository->findBy([], ['dateInscription' => 'DESC'], 5);

        return $this->render('admin/dashbord.html.twig', [
            'latestUsers' => $latestUsers
        ]);
    }

}

/*
 #[Route('/admin/dashboard', name: 'admin_dashboard')]
public function index(
    BilletRepository $billetRepository,
    MatchRepository $matchRepository,
    UtilisateurRepository $utilisateurRepository,
    StadeRepository $stadeRepository
): Response {
    return $this->render('admin/dashboard/index.html.twig', [
        'totalBillets' => $billetRepository->count([]),
        'revenusTotaux' => $billetRepository->calculateTotalRevenue(),
        'tauxRemplissage' => $matchRepository->getAverageFillRate(),
        'topMatchs' => $matchRepository->getTopMatches(3),
        'derniersUtilisateurs' => $utilisateurRepository->findBy([], ['id' => 'DESC'], 5),
    ]);
}

 */