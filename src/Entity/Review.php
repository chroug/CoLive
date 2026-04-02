<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: "review")]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['review:read']],
    paginationItemsPerPage: 10
)]
#[ApiFilter(OrderFilter::class, properties: ['note' => 'DESC', 'dateCreation' => 'DESC'])]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_avis", type: "integer")]
    #[Groups(['review:read'])]
    private ?int $id = null;

    #[ORM\Column(type: "integer")]
    #[Groups(['review:read'])]
    private int $note;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['review:read'])]
    private ?string $commentaire = null;

    #[ORM\Column(name: "date_creation", type: "datetime")]
    #[Groups(['review:read'])]
    private \DateTimeInterface $dateCreation;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?User $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: Announce::class, inversedBy: 'avis')]
    #[ORM\JoinColumn(name: "id_annonce", referencedColumnName: "id_annonce", nullable: false)]
    private ?Announce $annonce = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getNote(): int { return $this->note; }
    public function setNote(int $n): self { $this->note = $n; return $this; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $c): self { $this->commentaire = $c; return $this; }
    public function getDateCreation(): \DateTimeInterface { return $this->dateCreation; }
    public function getUtilisateur(): ?User { return $this->utilisateur; }
    public function setUtilisateur(?User $u): self { $this->utilisateur = $u; return $this; }
    public function getAnnonce(): ?Announce { return $this->annonce; }
    public function setAnnonce(?Announce $a): self { $this->annonce = $a; return $this; }
}
