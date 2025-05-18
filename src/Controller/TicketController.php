<?php

namespace App\Controller;

use App\Repository\BilletRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'app_tickets')]
    public function index(BilletRepository $billetRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $billets = $billetRepository->findByUser($user);
        
        return $this->render('customer/tickets/index.html.twig', [
            'billets' => $billets,
        ]);
    }
    
    #[Route('/ticket/{id}', name: 'app_ticket_show')]
    public function show(string $id, BilletRepository $billetRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $billet = $billetRepository->find($id);
        
        if (!$billet || $billet->getCommande()->getUser() !== $user) {
            throw $this->createNotFoundException('Billet non trouvé');
        }
        
        return $this->render('customer/tickets/show.html.twig', [
            'billet' => $billet,
        ]);
    }
    
    #[Route('/commandes', name: 'app_commandes')]
    public function commandes(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $commandes = $commandeRepository->findByUser($user);
        
        return $this->render('customer/tickets/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }
}