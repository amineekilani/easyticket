<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add', name: 'cart_add', methods: ['POST'])]
    public function add(Request $request, CartService $cartService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $item = [
            'match' => $data['match'],
            'stadium' => $data['stadium'],
            'section' => $data['section'],
            'seatNumber' => $data['seatNumber'],
            'price' => (float)$data['price'],
            'matchId' => $data['matchId']
        ];
        
        $cartService->addToCart($item);
        
        return $this->json([
            'success' => true,
            'count' => count($cartService->getCart())
        ]);
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['DELETE'])]
    public function remove(string $id, CartService $cartService): JsonResponse
    {
        $cartService->removeFromCart($id);
        
        return $this->json([
            'success' => true,
            'count' => count($cartService->getCart())
        ]);
    }

    #[Route('/cart/items', name: 'cart_items', methods: ['GET'])]
    public function items(CartService $cartService): JsonResponse
    {
        $cart = $cartService->getCart();
        $items = [];
        
        foreach ($cart as $id => $item) {
            $items[] = array_merge($item, ['id' => $id]);
        }
        
        return $this->json($items);
    }

    #[Route('/cart/count', name: 'cart_count', methods: ['GET'])]
    public function count(CartService $cartService): JsonResponse
    {
        return $this->json([
            'count' => count($cartService->getCart())
        ]);
    }
}