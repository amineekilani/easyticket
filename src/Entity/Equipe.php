<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'équipe est obligatoire")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le pays est obligatoire")]
    private ?string $pays = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = 'Actif';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        groups: ['upload']
    )]
    private $logoFile;

    #[ORM\Column(length: 10)]
    private ?string $abreviation = null;

    /**
     * @var Collection<int, MatchFootball>
     */
    #[ORM\OneToMany(targetEntity: MatchFootball::class, mappedBy: 'equipe1')]
    private Collection $matchFootballs;

    public function __construct()
    {
        $this->matchFootballs = new ArrayCollection();
    }

    // Getters et Setters...
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    public function setLogoFile($logoFile): void
    {
        $this->logoFile = $logoFile;
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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getAbreviation(): ?string
    {
        return $this->abreviation;
    }

    public function setAbreviation(string $abreviation): static
    {
        $this->abreviation = $abreviation;

        return $this;
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
            $matchFootball->setEquipe1($this);
        }

        return $this;
    }

    public function removeMatchFootball(MatchFootball $matchFootball): static
    {
        if ($this->matchFootballs->removeElement($matchFootball)) {
            // set the owning side to null (unless already changed)
            if ($matchFootball->getEquipe1() === $this) {
                $matchFootball->setEquipe1(null);
            }
        }

        return $this;
    }
}
