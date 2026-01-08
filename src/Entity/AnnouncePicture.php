<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "announce_picture")]
class AnnouncePicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_photoAnnonce", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;
    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $dateCreation;

    #[ORM\ManyToOne(targetEntity: Announce::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(name: "id_annonce", referencedColumnName: "id_annonce", nullable: false)]
    private ?Announce $annonce = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getContenu(): ?string { return $this->contenu; }

    public function setContenu(string $contenu): self { $this->contenu = $contenu; return $this; }
    public function getDateCreation(): \DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $d): self { $this->dateCreation = $d; return $this; }
    public function getAnnonce(): ?Announce { return $this->annonce; }
    public function setAnnonce(?Announce $a): self { $this->annonce = $a; return $this; }
}
