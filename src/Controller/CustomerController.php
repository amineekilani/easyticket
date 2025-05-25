<?php

namespace App\Controller;

use App\Entity\MatchFootball;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\StadeRepository;
use App\Repository\EquipeRepository;
use App\Repository\MatchFootballRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\EmailService;

final class CustomerController extends AbstractController
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

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

        $hasManualFilters = !empty(array_filter($filters));

        if ($hasManualFilters) {
            $matchs = $matchRepo->findWithFilters($filters);
        } else {
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

        $originalEmail = $user->getEmail();

        $form = $this->createForm(UserType::class, $user, [
            'is_customer' => true,
            'password_hasher' => $passwordHasher,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $emailChanged = $user->getEmail() !== $originalEmail;

            // Require current password for email or password changes
            if ($emailChanged || $plainPassword) {
                $currentPassword = $form->get('currentPassword')->getData();
                if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                    $form->get('currentPassword')->addError(new FormError('Le mot de passe actuel est incorrect.'));
                    return $this->render('customer/profile/edit.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
            }

            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            if ($emailChanged) {
                $user->setIsVerified(false);
                $this->sendEmailConfirmation($user, $entityManager);
                $this->sendEmailChangeNotification($originalEmail, $user);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès. Si vous avez changé votre email, veuillez vérifier votre nouvelle adresse.');
            return $this->redirectToRoute('app_customer');
        }

        return $this->render('customer/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function sendEmailConfirmation(User $user, EntityManagerInterface $entityManager): void
    {
        $token = bin2hex(random_bytes(32));
        $user->setConfirmationToken($token);
        $entityManager->flush();

        $this->emailService->send(
            'aminekilani901@gmail.com',
            $user->getEmail(),
            'EasyTicket | Confirmez votre nouvelle adresse email',
            'registration/confirmation_email.html.twig',
            [
                'user' => $user,
                'token' => $token,
            ]
        );
    }

    private function sendEmailChangeNotification(string $oldEmail, User $user): void
    {
        $this->emailService->send(
            'aminekilani901@gmail.com',
            $oldEmail,
            'EasyTicket | Changement de votre adresse email',
            'emails/email_change_notification.html.twig',
            [
                'user' => $user,
                'newEmail' => $user->getEmail(),
            ]
        );
    }
}