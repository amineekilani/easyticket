<?php

namespace App\Controller;

use App\Entity\Billet;
use App\Entity\Commande;
use App\Repository\MatchFootballRepository;
use App\Service\CartService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function checkout(Request $request, CartService $cartService, EntityManagerInterface $entityManager, MatchFootballRepository $matchRepository): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        
        $cart = $cartService->getCart();
        if (empty($cart)) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Votre panier est vide'], 400);
            }
            return $this->redirectToRoute('app_customer');
        }
        
        $user = $this->getUser();
        if (!$user) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Vous devez être connecté pour effectuer un paiement'], 401);
            }
            return $this->redirectToRoute('app_login');
        }
        
        // Créer la commande
        $commande = new Commande();
        $commande->setUser($user);
        
        // Calculer le total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'];
            
            // Créer le billet
            $match = $matchRepository->find($item['matchId']);
            if (!$match) {
                continue;
            }
            
            $billet = new Billet();
            $billet->setMatch($match)
                ->setSection($item['section'])
                ->setPrice($item['price'])
                ->generateQrCode();
                
            $commande->addBillet($billet);
        }
        
        // Ajouter les frais de service
        $total += 2;
        $commande->setTotal($total);
        
        // Stocker les détails de la commande
        $commande->setCommandeDetails(json_encode($cart));
        
        $entityManager->persist($commande);
        $entityManager->flush();
        
        $lineItems = [];
        foreach ($cart as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['match'] . ' - ' . $item['section'],
                    ],
                    'unit_amount' => $item['price'] * 100, // Stripe utilise les centimes
                ],
                'quantity' => 1,
            ];
        }
        
        // Ajouter les frais de service
        $lineItems[] = [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Frais de service',
                ],
                'unit_amount' => 2 * 100,
            ],
            'quantity' => 1,
        ];
        
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $this->generateUrl('payment_success', ['session_id' => '{CHECKOUT_SESSION_ID}'], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('payment_cancel', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
            
            // Mettre à jour la commande avec l'ID de session Stripe
            $commande->setStripeSessionId($session->id);
            $entityManager->flush();
            
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true, 'redirect_url' => $session->url]);
            }
            
            return $this->redirect($session->url, 303);
        } catch (\Exception $e) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Erreur lors de la création de la session de paiement: ' . $e->getMessage()], 500);
            }
            
            throw $e;
        }
    }

    #[Route('/payment/success', name: 'payment_success')]
    public function success(
        Request $request, 
        CartService $cartService, 
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response
    {
        $sessionId = $request->query->get('session_id');
        
        // Trouver et mettre à jour la commande
        $commandeRepository = $entityManager->getRepository(Commande::class);
        $commande = $commandeRepository->findByStripeSessionId($sessionId);
        
        if ($commande) {
            // Marquer la commande comme payée
            $commande->setPaid(true);
            $entityManager->flush();
            
            // Récupérer l'utilisateur et ses informations
            $user = $commande->getUser();
            $email = $user->getEmail();
            $name = $user->getName() ?? 'Client';
            
            // Préparer les détails de la commande pour l'email
            $orderDetails = [
                'numero' => $commande->getId(),
                'date' => $commande->getCreatedAt(),
                'total' => $commande->getTotal(),
                'billets' => []
            ];
            
            foreach ($commande->getBillets() as $billet) {
                $match = $billet->getMatch();
                $equipe1 = $match->getEquipe1() ? $match->getEquipe1()->getNom() : 'Équipe 1';
                $equipe2 = $match->getEquipe2() ? $match->getEquipe2()->getNom() : 'Équipe 2';
                
                $orderDetails['billets'][] = [
                    'match' => $equipe1 . ' vs ' . $equipe2,
                    'date' => $match->getDateEtHeure(),
                    'section' => $billet->getSection(),
                    'price' => $billet->getPrice(),
                    'qrCode' => $billet->getQrCode()
                ];
            }
            
            // Envoyer l'email de confirmation
            $emailService->sendPaymentConfirmation($email, $name, $orderDetails);
        }
        
        // Vider le panier
        $cartService->clearCart();
        
        return $this->render('customer/payment/success.html.twig', [
            'commande' => $commande
        ]);
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('customer/payment/cancel.html.twig');
    }
}