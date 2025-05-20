<?php

namespace App\Controller;

use App\Entity\MatchFootball;
use App\Form\UserType;
use App\Repository\StadeRepository;
use App\Repository\EquipeRepository;
use App\Repository\MatchFootballRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CustomerController extends AbstractController
{
    #[Route('/match/{id}', name: 'app_match_details')]
    public function details(MatchFootball $match): Response
    {
        return $this->render('customer/details.html.twig', [
            'match' => $match,
        ]);
    }
    #[Route('/', name: 'app_customer')]
    public function index(MatchFootballRepository $matchFootballRepository): Response
    {
        // Récupérer les 3 prochains matchs à venir
        $prochainMatchs = $matchFootballRepository->findNextMatches(3);

        return $this->render('customer/acceuil.html.twig', [
            'prochainMatchs' => $prochainMatchs,
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

    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_customer' => true, // Pass option to exclude admin-specific fields
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_customer');
        }

        return $this->render('customer/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
