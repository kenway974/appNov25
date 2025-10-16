<?php

namespace App\Entity;

use App\Repository\UserNeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserNeedRepository::class)]
class UserNeed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userNeeds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userNeeds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Need $need = null;

    #[ORM\Column]
    private ?int $priority = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column(nullable: true)]
    private ?array $notes = null;

    /**
     * @var Collection<int, UserAction>
     */
    #[ORM\OneToMany(targetEntity: UserAction::class, mappedBy: 'userNeed')]
    private Collection $userActions;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $lastUpdated = null;

    /**
     * @var Collection<int, UserNeedHistory>
     */
    #[ORM\OneToMany(targetEntity: UserNeedHistory::class, mappedBy: 'userNeed')]
    private Collection $userNeedHistories;

    public function __construct()
    {
        $this->userActions = new ArrayCollection();
        $this->userNeedHistories = new ArrayCollection();
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

    /**
     * @return Collection<int, UserNeedHistory>
     */
    public function getUserNeedHistories(): Collection
    {
        return $this->userNeedHistories;
    }

    public function addUserNeedHistory(UserNeedHistory $userNeedHistory): static
    {
        if (!$this->userNeedHistories->contains($userNeedHistory)) {
            $this->userNeedHistories->add($userNeedHistory);
            $userNeedHistory->setUserNeed($this);
        }

        return $this;
    }

    public function removeUserNeedHistory(UserNeedHistory $userNeedHistory): static
    {
        if ($this->userNeedHistories->removeElement($userNeedHistory)) {
            // set the owning side to null (unless already changed)
            if ($userNeedHistory->getUserNeed() === $this) {
                $userNeedHistory->setUserNeed(null);
            }
        }

        return $this;
    }
}
