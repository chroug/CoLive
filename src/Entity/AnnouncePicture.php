<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "photo_annonce")]
class PhotoAnnonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_photoAnnonce", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $url;

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
    public function getUrl(): string { return $this->url; }
    public function setUrl(string $url): self { $this->url = $url; return $this; }
    public function getDateCreation(): \DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $d): self { $this->dateCreation = $d; return $this; }
    public function getAnnonce(): ?Announce { return $this->annonce; }
    public function setAnnonce(?Announce $a): self { $this->annonce = $a; return $this; }
}
