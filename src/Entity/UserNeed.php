<?php

namespace App\Entity;

use App\Repository\UserNeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(
    name: 'need',
    indexes: [
        new ORM\Index(name: 'idx_priority', columns: ['priority']),
        new ORM\Index(name: 'idx_score', columns: ['score'])
    ]
)]

#[ORM\Entity(repositoryClass: UserNeedRepository::class)]
class UserNeed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userNeeds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un utilisateur est requis.')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userNeeds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Un besoin est requis.')]
    private ?Need $need = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La priorité est obligatoire.')]
    #[Assert\Type('integer')]
    #[Assert\Range(
        notInRangeMessage: 'La priorité doit être comprise entre {{ min }} et {{ max }}.',
        min: 1,
        max: 5
    )]
    private ?int $priority = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Le score est obligatoire.')]
    #[Assert\Type('integer')]
    #[Assert\Range(
        notInRangeMessage: 'Le score doit être compris entre {{ min }} et {{ max }}.',
        min: 0,
        max: 100
    )]
    private ?int $score = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type('array')]
    private ?array $notes = null;

    /**
     * @var Collection<int, UserAction>
     */
    #[ORM\OneToMany(targetEntity: UserAction::class, mappedBy: 'userNeed', cascade: ['persist', 'remove'])]
    #[Assert\Count(
        max: 3,
        maxMessage: 'Un besoin utilisateur ne peut pas avoir plus de 3 actions.'
    )]
    private Collection $userActions;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(\DateTime::class)]
    private ?\DateTime $lastUpdated = null;

    public function __construct()
    {
        $this->userActions = new ArrayCollection();
    }

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

    public function getNeed(): ?Need
    {
        return $this->need;
    }

    public function setNeed(?Need $need): static
    {
        $this->need = $need;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getNotes(): ?array
    {
        return $this->notes;
    }

    public function setNotes(?array $notes): static
    {
        $this->notes = $notes;

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
            $userAction->setUserNeed($this);
        }

        return $this;
    }

    public function removeUserAction(UserAction $userAction): static
    {
        if ($this->userActions->removeElement($userAction)) {
            // set the owning side to null (unless already changed)
            if ($userAction->getUserNeed() === $this) {
                $userAction->setUserNeed(null);
            }
        }

        return $this;
    }

    public function getLastUpdated(): ?\DateTime
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(?\DateTime $lastUpdated): static
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }
}
