<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "utilisateur")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_utilisateur", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(name: "mot_de_passe", length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(name: "date_creation_compte", type: "datetime")]
    private \DateTimeInterface $dateCreationCompte;

    #[ORM\Column(type: "integer")]
    private int $role = 1;

    public function __construct()
    {
        $this->dateCreationCompte = new \DateTime();
    }

    // --------- UserInterface & PasswordAuthenticatedUserInterface ---------

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void {}

    // ------------------- Getters & Setters -------------------

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getTel(): ?string { return $this->tel; }
    public function setTel(?string $tel): self { $this->tel = $tel; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getAvatar(): ?string { return $this->avatar; }
    public function setAvatar(?string $avatar): self { $this->avatar = $avatar; return $this; }

    public function getDateCreationCompte(): \DateTimeInterface { return $this->dateCreationCompte; }
    public function setDateCreationCompte(\DateTimeInterface $dateCreationCompte): self {
        $this->dateCreationCompte = $dateCreationCompte; return $this;
    }

    public function getRole(): int {
        return $this->role;
    }
    public function setRole(int $role): self { $this->role = $role; return $this; }
}
