<?php

namespace App\Controller;

use App\Service\CartService;
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
    public function checkout(Request $request, CartService $cartService): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        
        $cart = $cartService->getCart();
        if (empty($cart)) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Votre panier est vide'], 400);
            }
            return $this->redirectToRoute('app_customer');
        }
        
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
                'success_url' => $this->generateUrl('payment_success', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('payment_cancel', [], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
            
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
    public function success(CartService $cartService): Response
    {
        $cartService->clearCart();
        return $this->render('customer/payment/success.html.twig');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('customer/payment/cancel.html.twig');
    }
}