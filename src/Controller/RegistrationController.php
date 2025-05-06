<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailService $emailService)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Generate a confirmation token
            $token = bin2hex(random_bytes(32));
            $user->setConfirmationToken($token);
            $user->setIsVerified(false);
            $user->setDateInscription(new \DateTimeImmutable());

            // Persist the user
            $entityManager->persist($user);
            $entityManager->flush();

            // Send confirmation email via Brevo
            $this->emailService->send(
                'aminekilani901@gmail.com',
                $user->getEmail(),
                'EasyTicket | Mail de confirmation',
                'registration/confirmation_email.html.twig',
                [
                    'user' => $user,
                    'token' => $token,
                ]
            );

            $this->addFlash('success', 'Inscription réussie ! Veuillez vérifier votre email pour confirmer.');

            return $this->redirectToRoute('app_equipe_index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $token = $request->query->get('token');
        $user = $entityManager->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            $this->addFlash('verify_email_error', $translator->trans('Le lien de vérification est invalide ou a expiré.', [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        // Mark the user as verified
        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre adresse email a été vérifiée.');

        return $this->redirectToRoute('app_equipe_index');
    }
}