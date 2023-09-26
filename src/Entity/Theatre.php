<?php

namespace App\Entity;

use App\Repository\TheatreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TheatreRepository::class)]
class Theatre extends Utilisateur
{

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $qrcode = null;

    #[ORM\OneToMany(mappedBy: 'theatre', targetEntity: Ouvreur::class, orphanRemoval: true)]
    private Collection $ouvreurs;

    #[ORM\OneToMany(mappedBy: 'theatre', targetEntity: Pourboire::class, orphanRemoval: true)]
    private Collection $pourboires;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    public function __construct()
    {
        $this->ouvreurs = new ArrayCollection();
        $this->pourboires = new ArrayCollection();
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

    public function getQrcode(): ?string
    {
        return $this->qrcode;
    }

    public function setQrcode(string $qrcode): static
    {
        $this->qrcode = $qrcode;

        return $this;
    }

    /**
     * @return Collection<int, Ouvreur>
     */
    public function getOuvreurs(): Collection
    {
        return $this->ouvreurs;
    }

    public function addOuvreur(Ouvreur $ouvreur): static
    {
        if (!$this->ouvreurs->contains($ouvreur)) {
            $this->ouvreurs->add($ouvreur);
            $ouvreur->setTheatre($this);
        }

        return $this;
    }

    public function removeOuvreur(Ouvreur $ouvreur): static
    {
        if ($this->ouvreurs->removeElement($ouvreur)) {
            // set the owning side to null (unless already changed)
            if ($ouvreur->getTheatre() === $this) {
                $ouvreur->setTheatre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pourboire>
     */
    public function getPourboires(): Collection
    {
        return $this->pourboires;
    }

    public function addPourboire(Pourboire $pourboire): static
    {
        if (!$this->pourboires->contains($pourboire)) {
            $this->pourboires->add($pourboire);
            $pourboire->setTheatre($this);
        }

        return $this;
    }

    public function removePourboire(Pourboire $pourboire): static
    {
        if ($this->pourboires->removeElement($pourboire)) {
            // set the owning side to null (unless already changed)
            if ($pourboire->getTheatre() === $this) {
                $pourboire->setTheatre(null);
            }
        }

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }
}
