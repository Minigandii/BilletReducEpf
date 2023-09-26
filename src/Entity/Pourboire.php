<?php

namespace App\Entity;

use App\Repository\PourboireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PourboireRepository::class)]
class Pourboire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $moyenPaiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'pourboires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?theatre $theatre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMoyenPaiement(): ?string
    {
        return $this->moyenPaiement;
    }

    public function setMoyenPaiement(string $moyenPaiement): static
    {
        $this->moyenPaiement = $moyenPaiement;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTheatre(): ?theatre
    {
        return $this->theatre;
    }

    public function setTheatre(?theatre $theatre): static
    {
        $this->theatre = $theatre;

        return $this;
    }
}
