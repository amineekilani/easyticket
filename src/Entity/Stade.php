<?php

namespace App\Entity;

use App\Repository\StadeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StadeRepository::class)]
class Stade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(nullable: true)]
    private ?int $capaciteVirage = null;

    #[ORM\Column(nullable: true)]
    private ?int $capacitePelouse = null;

    #[ORM\Column(nullable: true)]
    private ?int $capaciteEnceinte = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Image(maxSize: '2M', mimeTypes: ['image/jpeg', 'image/png', 'image/webp'], groups: ['upload'])]
    private $imageFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCapaciteVirage(): ?int
    {
        return $this->capaciteVirage;
    }

    public function setCapaciteVirage(?int $capaciteVirage): static
    {
        $this->capaciteVirage = $capaciteVirage;

        return $this;
    }

    public function getCapacitePelouse(): ?int
    {
        return $this->capacitePelouse;
    }

    public function setCapacitePelouse(?int $capacitePelouse): static
    {
        $this->capacitePelouse = $capacitePelouse;

        return $this;
    }

    public function getCapaciteEnceinte(): ?int
    {
        return $this->capaciteEnceinte;
    }

    public function setCapaciteEnceinte(?int $capaciteEnceinte): static
    {
        $this->capaciteEnceinte = $capaciteEnceinte;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile($imageFile): void
    {
        $this->imageFile = $imageFile;
    }
}
