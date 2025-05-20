<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void {
        $email = (new TemplatedEmail())
            ->from(new Address($from, 'EasyTicket'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)

            ->context($context);

        $this->mailer->send($email);
    }

    public function sendPasswordResetEmail(string $to, string $name, string $resetUrl): void
    {
        $this->send(
            'aminekilani901@gmail.com',
            $to,
            'Réinitialisation de votre mot de passe - EasyTicket',
            'emails/reset_password.html.twig',
            [
                'resetUrl' => $resetUrl,
                'name' => $name,
                'expireTime' => '1 heure'
            ]
        );
    }

    public function sendPaymentConfirmation(string $to, string $name, array $orderDetails): void
    {
        $this->send(
            'aminekilani901@gmail.com',
            $to,
            'Confirmation de votre commande - EasyTicket',
            'emails/payment_confirmation.html.twig',
            [
                'name' => $name,
                'order' => $orderDetails,
                'date' => new \DateTime()
            ]
        );
    }
}