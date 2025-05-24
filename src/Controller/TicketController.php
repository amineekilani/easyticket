<?php

namespace App\Controller;

use App\Repository\BilletRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
        
        return $this->render('customer/commandes/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }
    
    #[Route('/commande/{id}', name: 'app_commande_show')]
    public function showCommande(string $id, CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $commande = $commandeRepository->find($id);
        
        if (!$commande || $commande->getUser() !== $user) {
            throw $this->createNotFoundException('Commande non trouvée');
        }
        
        return $this->render('customer/commandes/show.html.twig', [
            'commande' => $commande,
        ]);
    }
    
    #[Route('/ticket/{id}/cancel', name: 'app_ticket_cancel', methods: ['POST'])]
    public function cancel(string $id, BilletRepository $billetRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        $billet = $billetRepository->find($id);
        
        if (!$billet || $billet->getCommande()->getUser() !== $user) {
            throw $this->createNotFoundException('Billet non trouvé');
        }
        
        // Vérifier si le match est dans plus de 24 heures
        $matchDateTime = $billet->getMatch()->getDateEtHeure();
        $now = new \DateTime();
        $timeBeforeMatch = $matchDateTime->getTimestamp() - $now->getTimestamp();
        
        if ($timeBeforeMatch <= 86400) { // 86400 seconds = 24 hours
            $this->addFlash('error', 'L\'annulation n\'est possible que 24 heures avant le match.');
            return $this->redirectToRoute('app_tickets');
        }
        
        // Logique d'annulation (à personnaliser selon vos besoins)
        // Par exemple, marquer le billet comme annulé ou le supprimer
        
        // Option 1: Supprimer le billet
        $entityManager->remove($billet);
        
        // Option 2: Ou le marquer comme annulé (si vous avez un champ status dans votre entité Billet)
        // $billet->setStatus('annulé');
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Votre billet a été annulé avec succès.');
        
        return $this->redirectToRoute('app_tickets');
    }
}