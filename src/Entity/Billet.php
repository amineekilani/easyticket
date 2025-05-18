<?php

namespace App\Entity;

use App\Repository\BilletRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BilletRepository::class)]
class Billet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'billets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;
    
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?MatchFootball $match = null;
    
    #[ORM\Column(length: 255)]
    private ?string $section = null;
    
    #[ORM\Column(length: 255)]
    private ?string $seatNumber = null;
    
    #[ORM\Column]
    private ?float $price = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qrCode = null;
    
    #[ORM\Column(length: 255)]
    private ?string $status = 'valid';

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getCommande(): ?Commande
    {
        return $this->commande;
    }
    
    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;
        
        return $this;
    }
    
    public function getMatch(): ?MatchFootball
    {
        return $this->match;
    }
    
    public function setMatch(?MatchFootball $match): self
    {
        $this->match = $match;
        
        return $this;
    }
    
    public function getSection(): ?string
    {
        return $this->section;
    }
    
    public function setSection(string $section): self
    {
        $this->section = $section;
        
        return $this;
    }
    
    public function getSeatNumber(): ?string
    {
        return $this->seatNumber;
    }
    
    public function setSeatNumber(string $seatNumber): self
    {
        $this->seatNumber = $seatNumber;
        
        return $this;
    }
    
    public function getPrice(): ?float
    {
        return $this->price;
    }
    
    public function setPrice(float $price): self
    {
        $this->price = $price;
        
        return $this;
    }
    
    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }
    
    public function setQrCode(?string $qrCode): self
    {
        $this->qrCode = $qrCode;
        
        return $this;
    }
    
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): self
    {
        $this->status = $status;
        
        return $this;
    }
    
    public function generateQrCode(): self
    {
        // Générer un ID unique pour le QR code
        $this->qrCode = 'ticket_' . uniqid() . '_' . $this->id;
        
        return $this;
    }
}