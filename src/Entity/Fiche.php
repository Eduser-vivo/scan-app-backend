<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;


/**
 * @ORM\Entity(repositoryClass="App\Repository\FicheRepository")
 * @ApiFilter(
 *      DateFilter::class,
 *      properties={
 *          "date"
 *      }
 *  )
 * 
 * @ApiResource(
 *  attributes={"order"= {"id": "DESC"}}
 * )
 */
class Fiche
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 3, minMessage = "veuillez saisir un nom valide")
     * @Assert\NotBlank(message="veuillez remplir ce champ")
     */
    private $signataire;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 3, minMessage = "veuillez saisir une adresse valide")
     * @Assert\NotBlank(message="veuillez remplir ce champ")
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 3, minMessage = "veuillez saisir un nom valide")
     * @Assert\NotBlank(message="veuillez remplir ce champ")
     */
    private $creantier;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(message="veuillez mettre un montant valide")
     */
    private $montant;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min = 10, minMessage = "veuillez entrer un motif valide")
     * @Assert\NotBlank(message="veuillez remplir ce champ")
     */
    private $motif;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 3, minMessage = "veuillez saisir une adresse valide")
     * @Assert\NotBlank(message="veuillez remplir ce champ")
     */
    private $lieu;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="fiches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSignataire(): ?string
    {
        return $this->signataire;
    }

    public function setSignataire(string $signataire): self
    {
        $this->signataire = $signataire;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCreantier(): ?string
    {
        return $this->creantier;
    }

    public function setCreantier(string $creantier): self
    {
        $this->creantier = $creantier;

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

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return User
     */
    public function getUtilisateur(): User
    {
        return $this->utilisateur;
    }

    /**
     * @param User $utilisateur
     */
    public function setUtilisateur(User $utilisateur) : self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
