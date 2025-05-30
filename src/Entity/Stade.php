<?php

namespace App\Entity;

use App\Repository\StadeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $photo;

    /**
     * @var Collection<int, MatchFootball>
     */
    #[ORM\OneToMany(targetEntity: MatchFootball::class, mappedBy: 'stade')]
    private Collection $matchFootballs;

    public function __construct()
    {
        $this->matchFootballs = new ArrayCollection();
    }

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

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): void
    {
        $this->photo = $photo;
    }

    public function getCapacite(): int
    {
        return $this->capaciteVirage + $this->capacitePelouse + $this->capaciteEnceinte;
    }

    /**
     * @return Collection<int, MatchFootball>
     */
    public function getMatchFootballs(): Collection
    {
        return $this->matchFootballs;
    }

    public function addMatchFootball(MatchFootball $matchFootball): static
    {
        if (!$this->matchFootballs->contains($matchFootball)) {
            $this->matchFootballs->add($matchFootball);
            $matchFootball->setStade($this);
        }

        return $this;
    }

    public function removeMatchFootball(MatchFootball $matchFootball): static
    {
        if ($this->matchFootballs->removeElement($matchFootball)) {
            // set the owning side to null (unless already changed)
            if ($matchFootball->getStade() === $this) {
                $matchFootball->setStade(null);
            }
        }

        return $this;
    }
}
