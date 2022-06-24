<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CongeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CongeRepository::class)]
class Conge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $demandeur;



    #[ORM\Column(type: 'date')]
    #[Assert\LessThan(
        'today +1year',
        message:'Merci de renseigner une date de début plus proche')]
    #[Assert\GreaterThan(
        'today -7days', 
        message:'Merci de renseigner une date de début plus proche')]
    #[Assert\Expression(
        "this.getDateDebut().format('D') != 'Sat' and this.getDateDebut().format('D') != 'Sun'" ,
        message:"Merci de renseigner une date de début qui n'est pas un jour de week-end")]        
    private $dateDebut;

    #[ORM\Column(type: 'date',nullable: true)]
    #[Assert\Expression(
        "this.getDateFin() == null or (this.getDateFin() > this.getDateDebut())" ,
        message:"Merci de renseigner une date de fin postérieur à la date de début")]
    #[Assert\Expression(
        "this.getDateFin() == null or (this.getDateFin().format('D') != 'Sat' and this.getDateFin().format('D') != 'Sun')" ,
        message:"Merci de renseigner une date de fin qui n'est pas un jour de week-end")]       
    private $dateFin;

    #[ORM\Column(type: 'string', length: 50)]
    private $periodeDebut;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $periodeFin;

    #[ORM\Column(type: 'float')]
    private $nb_jour;

    #[ORM\Column(type: 'string', length: 50)]
    private $type;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $motif;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $validated_by;

    #[ORM\Column(type: 'string', length: 255)]
    private $statut;

    #[ORM\Column(type: 'date', nullable: true)]
    private $reply_at;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemandeurName(): string
    {
        return $this->demandeur->getUsername();
    }

    public function getDemandeur(): ?User
    {
        return $this->demandeur;
    }

    public function setDemandeur(?User $demandeur): self
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getPeriodeDebut(): ?string
    {
        return $this->periodeDebut;
    }

    public function setPeriodeDebut(string $periodeDebut): self
    {
        $this->periodeDebut = $periodeDebut;

        return $this;
    }

    public function getPeriodeFin(): ?string
    {
        return $this->periodeFin;
    }

    public function setPeriodeFin(?string $periodeFin): self
    {
        $this->periodeFin = $periodeFin;

        return $this;
    }

    public function getNbJour(): ?float
    {
        return $this->nb_jour;
    }

    public function setNbJour(float $nb_jour): self
    {
        $this->nb_jour = $nb_jour;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function isEnCours(): bool
    {
        $date = new DateTime();
        if($this->getDateFin() == null && ($this->getDateDebut()->format('Y-m-d') == $date->format('Y-m-d'))) {
                return true;
        } else if ($this->getDateFin() >= $date && $this->getDateDebut() <= $date) {
            return true;
        }
        return false;
    }

    public function getValidatedBy(): ?User
    {
        return $this->validated_by;
    }

    public function setValidatedBy(?User $validated_by): self
    {
        $this->validated_by = $validated_by;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getReplyAt(): ?\DateTimeInterface
    {
        return $this->reply_at;
    }

    public function setReplyAt(?\DateTimeInterface $reply_at): self
    {
        $this->reply_at = $reply_at;

        return $this;
    }

}
