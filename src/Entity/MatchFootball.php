<?php

namespace App\Entity;

use App\Repository\MatchFootballRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchFootballRepository::class)]
class MatchFootball
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matchFootballs')]
    private ?Equipe $equipe1 = null;

    #[ORM\ManyToOne(inversedBy: 'matchFootballs')]
    private ?Equipe $equipe2 = null;

    #[ORM\ManyToOne(inversedBy: 'matchFootballs')]
    private ?Stade $stade = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEtHeure = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrBilletsVirage = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixBilletVirage = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrBilletsPelouse = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixBilletPelouse = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrBilletsEnceinte = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixBilletEnceinte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipe1(): ?Equipe
    {
        return $this->equipe1;
    }

    public function setEquipe1(?Equipe $equipe1): static
    {
        $this->equipe1 = $equipe1;

        return $this;
    }

    public function getEquipe2(): ?Equipe
    {
        return $this->equipe2;
    }

    public function setEquipe2(?Equipe $equipe2): static
    {
        $this->equipe2 = $equipe2;

        return $this;
    }

    public function getStade(): ?Stade
    {
        return $this->stade;
    }

    public function setStade(?Stade $stade): static
    {
        $this->stade = $stade;

        return $this;
    }

    public function getDateEtHeure(): ?\DateTimeInterface
    {
        return $this->dateEtHeure;
    }

    public function setDateEtHeure(\DateTimeInterface $dateEtHeure): static
    {
        $this->dateEtHeure = $dateEtHeure;

        return $this;
    }

    public function getNbrBilletsVirage(): ?int
    {
        return $this->nbrBilletsVirage;
    }

    public function setNbrBilletsVirage(?int $nbrBilletsVirage): static
    {
        $this->nbrBilletsVirage = $nbrBilletsVirage;

        return $this;
    }

    public function getPrixBilletVirage(): ?float
    {
        return $this->prixBilletVirage;
    }

    public function setPrixBilletVirage(?float $prixBilletVirage): static
    {
        $this->prixBilletVirage = $prixBilletVirage;

        return $this;
    }

    public function getNbrBilletsPelouse(): ?int
    {
        return $this->nbrBilletsPelouse;
    }

    public function setNbrBilletsPelouse(?int $nbrBilletsPelouse): static
    {
        $this->nbrBilletsPelouse = $nbrBilletsPelouse;

        return $this;
    }

    public function getPrixBilletPelouse(): ?float
    {
        return $this->prixBilletPelouse;
    }

    public function setPrixBilletPelouse(?float $prixBilletPelouse): static
    {
        $this->prixBilletPelouse = $prixBilletPelouse;

        return $this;
    }

    public function getNbrBilletsEnceinte(): ?int
    {
        return $this->nbrBilletsEnceinte;
    }

    public function setNbrBilletsEnceinte(?int $nbrBilletsEnceinte): static
    {
        $this->nbrBilletsEnceinte = $nbrBilletsEnceinte;

        return $this;
    }

    public function getPrixBilletEnceinte(): ?float
    {
        return $this->prixBilletEnceinte;
    }

    public function setPrixBilletEnceinte(?float $prixBilletEnceinte): static
    {
        $this->prixBilletEnceinte = $prixBilletEnceinte;

        return $this;
    }
}
