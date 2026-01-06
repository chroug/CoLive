<?php

namespace App\Entity;

use App\Repository\AnnounceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnounceRepository::class)]
#[ORM\Table(name: "announce")]
class Announce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_annonce", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: "integer")]
    private ?int $nb_pieces = null;

    #[ORM\Column(type: "float")]
    private ?float $prix = null;

    #[ORM\Column(type: "float")]
    private ?float $latitude = null;

    #[ORM\Column(type: "float")]
    private ?float $longitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $equipements = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $regle = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateCreation;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $disponibilite_debut;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $disponibilite_fin;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code_postal = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $surface = null;

    // --- RELATIONS ---

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'annonces')]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: Review::class)]
    private Collection $avis;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: AnnouncePicture::class)]
    private Collection $photos;

    // Relation via l'entité UserLikes
    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: UserLikes::class, cascade: ['persist', 'remove'])]
    private Collection $likes;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->disponibilite_debut = new \DateTime();
        $this->disponibilite_fin = new \DateTime();
        $this->avis = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->likes = new ArrayCollection(); // Changé ici
    }

    // Getters / Setters simplifiés
    public function getId(): ?int { return $this->id; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $t): self { $this->titre = $t; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $d): self { $this->description = $d; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $t): self { $this->type = $t; return $this; }
    public function getNbPieces(): ?int { return $this->nb_pieces; }
    public function setNbPieces(int $n): self { $this->nb_pieces = $n; return $this; }
    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $p): self { $this->prix = $p; return $this; }
    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(float $l): self { $this->latitude = $l; return $this; }
    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(float $l): self { $this->longitude = $l; return $this; }
    public function getEquipements(): ?string { return $this->equipements; }
    public function setEquipements(?string $e): self { $this->equipements = $e; return $this; }
    public function getRegle(): ?string { return $this->regle; }
    public function setRegle(?string $r): self { $this->regle = $r; return $this; }
    public function getDateCreation(): \DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $d): self { $this->dateCreation = $d; return $this; }
    public function getDisponibiliteDebut(): \DateTimeInterface { return $this->disponibilite_debut; }
    public function setDisponibiliteDebut(\DateTimeInterface $d): self { $this->disponibilite_debut = $d; return $this; }
    public function getDisponibiliteFin(): \DateTimeInterface { return $this->disponibilite_fin; }
    public function setDisponibiliteFin(\DateTimeInterface $d): self { $this->disponibilite_fin = $d; return $this; }
    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(string $a): self { $this->adresse = $a; return $this; }
    public function getVille(): ?string { return $this->ville; }
    public function setVille(string $v): self { $this->ville = $v; return $this; }

    public function getUtilisateur(): ?User { return $this->utilisateur; }
    public function setUtilisateur(?User $u): self { $this->utilisateur = $u; return $this; }
    public function getAvis(): Collection { return $this->avis; }
    public function getPhotos(): Collection { return $this->photos; }
    public function getLikes(): Collection { return $this->likes; }
    public function getCodePostal(): ?string { return $this->code_postal; }
    public function setCodePostal(?string $cp): self { $this->code_postal = $cp; return $this; }

    public function getSurface(): ?float { return $this->surface; }
    public function setSurface(?float $s): self { $this->surface = $s; return $this; }
}
