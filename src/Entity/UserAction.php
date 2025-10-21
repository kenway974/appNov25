<?php

namespace App\Entity;

use App\Repository\UserActionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserActionRepository::class)]
class UserAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userActions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un utilisateur est requis.')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userActions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Une action est requise.')]
    private ?Action $action = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(\DateTime::class)]
    #[Assert\GreaterThanOrEqual(
        propertyPath: 'startDate',
        message: 'La date limite doit être postérieure à la date de début.'
    )]
    private ?\DateTime $deadline = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type(\DateTime::class)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('integer')]
    #[Assert\PositiveOrZero(message: 'La fréquence doit être un nombre positif.')]
    #[Assert\GreaterThanOrEqual(value: 1, message: 'La fréquence minimale est de 1.')]
    private ?int $frequency = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('integer')]
    #[Assert\PositiveOrZero(message: 'Le nombre de complétions doit être positif.')]
    private ?int $completions = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('bool')]
    private ?bool $isChecked = null;

    #[ORM\ManyToOne(inversedBy: 'userActions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un besoin utilisateur est requis.')]
    private ?UserNeed $userNeed = null;

    #[ORM\OneToOne(mappedBy: 'userAction', cascade: ['persist', 'remove'])]
    private ?Notification $notification = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('bool')]
    private ?bool $isRecurring = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(\DateTime::class)]
    private ?\DateTime $lastUpdate = null;

    #[ORM\Column(length: 25)]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(
        choices: ['à faire', 'fait'],
        message: 'Le statut doit être "à faire" ou "fait".'
    )]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAction(): ?Action
    {
        return $this->action;
    }

    public function setAction(?Action $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getDeadline(): ?\DateTime
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTime $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getFrequency(): ?int
    {
        return $this->frequency;
    }

    public function setFrequency(?int $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getCompletions(): ?int
    {
        return $this->completions;
    }

    public function setCompletions(?int $completions): static
    {
        $this->completions = $completions;

        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->isChecked;
    }

    public function setIsChecked(?bool $isChecked): static
    {
        $this->isChecked = $isChecked;

        return $this;
    }

    public function getUserNeed(): ?UserNeed
    {
        return $this->userNeed;
    }

    public function setUserNeed(?UserNeed $userNeed): static
    {
        $this->userNeed = $userNeed;

        return $this;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): static
    {
        // unset the owning side of the relation if necessary
        if ($notification === null && $this->notification !== null) {
            $this->notification->setUserAction(null);
        }

        // set the owning side of the relation if necessary
        if ($notification !== null && $notification->getUserAction() !== $this) {
            $notification->setUserAction($this);
        }

        $this->notification = $notification;

        return $this;
    }

    public function isRecurring(): ?bool
    {
        return $this->isRecurring;
    }

    public function setIsRecurring(?bool $isRecurring): static
    {
        $this->isRecurring = $isRecurring;

        return $this;
    }

    public function getLastUpdate(): ?\DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTime $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
