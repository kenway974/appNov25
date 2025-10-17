<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 25)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dashboardIllustration = null;

    #[Vich\UploadableField(mapping: "user_dashboard", fileNameProperty: "dashboardIllustration")]
    private ?File $dashboardIllustrationFile = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;



    /**
     * @var Collection<int, UserNeed>
     */
    #[ORM\OneToMany(targetEntity: UserNeed::class, mappedBy: 'user')]
    private Collection $userNeeds;

    /**
     * @var Collection<int, UserAction>
     */
    #[ORM\OneToMany(targetEntity: UserAction::class, mappedBy: 'user')]
    private Collection $userActions;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeCustomerId = null;

    public function __construct()
    {
        $this->userNeeds = new ArrayCollection();
        $this->userActions = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        if (!in_array('ROLE_SUBSCRIBER', $roles)) {
            $roles[] = 'ROLE_SUBSCRIBER';
        }

        return array_unique($roles);
    }

   

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        unset($data["\0".self::class."\0dashboardIllustrationFile"]);
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function setDashboardIllustrationFile(?File $file = null): void
    {
        $this->dashboardIllustrationFile = $file;

        if ($file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getDashboardIllustrationFile(): ?File
    {
        return $this->dashboardIllustrationFile;
    }

    public function getDashboardIllustration(): ?string
    {
        return $this->dashboardIllustration;
    }

    public function setDashboardIllustration(?string $dashboardIllustration): void
    {
        $this->dashboardIllustration = $dashboardIllustration;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    /**
     * @return Collection<int, UserNeed>
     */
    public function getUserNeeds(): Collection
    {
        return $this->userNeeds;
    }

    public function addUserNeed(UserNeed $userNeed): static
    {
        if (!$this->userNeeds->contains($userNeed)) {
            $this->userNeeds->add($userNeed);
            $userNeed->setUser($this);
        }

        return $this;
    }

    public function removeUserNeed(UserNeed $userNeed): static
    {
        if ($this->userNeeds->removeElement($userNeed)) {
            // set the owning side to null (unless already changed)
            if ($userNeed->getUser() === $this) {
                $userNeed->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserAction>
     */
    public function getUserActions(): Collection
    {
        return $this->userActions;
    }

    public function addUserAction(UserAction $userAction): static
    {
        if (!$this->userActions->contains($userAction)) {
            $this->userActions->add($userAction);
            $userAction->setUser($this);
        }

        return $this;
    }

    public function removeUserAction(UserAction $userAction): static
    {
        if ($this->userActions->removeElement($userAction)) {
            // set the owning side to null (unless already changed)
            if ($userAction->getUser() === $this) {
                $userAction->setUser(null);
            }
        }

        return $this;
    }

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
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): static
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
    }
}
