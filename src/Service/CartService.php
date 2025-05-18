<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    public function addToCart(array $item): void
    {
        $session = $this->requestStack->getSession();
        $cart = $this->getCart();
        
        // Générer un ID unique pour l'item
        $itemId = md5(serialize($item));
        $cart[$itemId] = $item;
        
        $session->set('cart', $cart);
    }

    public function removeFromCart(string $itemId): void
    {
        $session = $this->requestStack->getSession();
        $cart = $this->getCart();
        
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            $session->set('cart', $cart);
        }
    }

    public function clearCart(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove('cart');
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['price'];
        }
        return $total;
    }
}