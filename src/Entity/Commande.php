<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Produit;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommandeRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero()]
    private $quantite;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThan(value:5)]
    private $montant;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(callback:"getStatut")]
    private $etat;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Type("datetime")]
    private $dt_enregistrement;

    
    #[ORM\ManyToOne(targetEntity:Produit::class, inversedBy:"commandes", cascade:["persist"])]
    private $produit;

    #[ORM\ManyToOne(targetEntity:User::class, inversedBy:"commandes", cascade:["persist"])]
    private $user;
    

    public function __construct(){

        $tz = new \DateTimeZone('Europe/Paris');
        $now = new \DateTime();
        $now->setTimezone($tz);
        $this->setDtEnregistrement($now); 

    }

    public function getStatut() :array{
        return ["En cours de traitement", "Envoyé", "Livré"];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDtEnregistrement(): ?\DateTimeInterface
    {
        return $this->dt_enregistrement;
    }

    public function setDtEnregistrement(\DateTimeInterface $dt_enregistrement): self
    {
        $this->dt_enregistrement = $dt_enregistrement;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
