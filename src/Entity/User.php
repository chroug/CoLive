<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "user")]
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

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: "mot_de_passe", length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(name: "date_creation_compte", type: "datetime")]
    private \DateTimeInterface $dateCreationCompte;

    #[ORM\Column(type: "integer")]
    private int $role = 1;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Announce::class)]
    private Collection $annonces;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: UserLikes::class, cascade: ['persist', 'remove'])]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Review::class)]
    private Collection $avis;

    #[ORM\ManyToMany(targetEntity: self::class)]
    #[ORM\JoinTable(name: "user_contacts")]
    #[ORM\JoinColumn(name: "user_source_id", referencedColumnName: "id_utilisateur")]
    #[ORM\InverseJoinColumn(name: "user_target_id", referencedColumnName: "id_utilisateur")]
    private Collection $contacts;

    #[ORM\OneToMany(mappedBy: 'locataire', targetEntity: Reservation::class)]
    private Collection $reservations;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'recipient')]
    private Collection $notifications;

    public function __construct()
    {
        $this->dateCreationCompte = new \DateTime();
        $this->annonces = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getUserIdentifier(): string { return $this->email; }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->role === 2) {
            $roles[] = 'ROLE_ADMIN';
        }
        return array_unique($roles);
    }

    public function eraseCredentials(): void {}

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getTel(): ?string { return $this->tel; }
    public function setTel(?string $tel): self { $this->tel = $tel; return $this; }
    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): self { $this->ville = $ville; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getAvatar(): ?string { return $this->avatar; }
    public function setAvatar(?string $avatar): self { $this->avatar = $avatar; return $this; }
    public function getDateCreationCompte(): \DateTimeInterface { return $this->dateCreationCompte; }
    public function setDateCreationCompte(\DateTimeInterface $d): self { $this->dateCreationCompte = $d; return $this; }
    public function getRole(): int { return $this->role; }
    public function setRole(int $role): self { $this->role = $role; return $this; }
    public function getAnnonces(): Collection { return $this->annonces; }
    public function getLikes(): Collection { return $this->likes; }
    public function getAvis(): Collection { return $this->avis; }
    public function getContacts(): Collection { return $this->contacts; }

    public function addContact(self $contact): self
    {
        if (!$this->contacts->contains($contact) && $contact !== $this) {
            $this->contacts->add($contact);
        }
        return $this;
    }

    public function removeContact(self $contact): self
    {
        $this->contacts->removeElement($contact);
        return $this;
    }

    public function getReservations(): Collection { return $this->reservations; }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setRecipient($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getRecipient() === $this) {
                $notification->setRecipient(null);
            }
        }

        return $this;
    }
}
