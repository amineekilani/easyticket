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
        $cart = $session->get('cart', []);
        
        // Ajouter un identifiant unique pour chaque item
        $item['id'] = uniqid();
        
        $cart[] = $item;
        $session->set('cart', $cart);
    }
    
    public function removeFromCart(string $id): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        
        foreach ($cart as $key => $item) {
            if ($item['id'] === $id) {
                unset($cart[$key]);
                break;
            }
        }
        
        $session->set('cart', array_values($cart));
    }
    
    public function clearCart(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove('cart');
    }
}