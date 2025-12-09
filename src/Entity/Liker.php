<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "liker")]
class Liker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Annonce::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(name: "id_annonce", referencedColumnName: "id_annonce", nullable: false)]
    private ?Annonce $annonce = null;

    public function getId(): ?int { return $this->id; }

    public function getUtilisateur(): ?User { return $this->utilisateur; }
    public function setUtilisateur(?User $utilisateur): self { $this->utilisateur = $utilisateur; return $this; }

    public function getAnnonce(): ?Annonce { return $this->annonce; }
    public function setAnnonce(?Annonce $annonce): self { $this->annonce = $annonce; return $this; }
}
