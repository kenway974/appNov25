<?php

namespace App\Entity;

use App\Repository\NeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NeedRepository::class)]
class Need
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $type = null;

    /**
     * @var Collection<int, UserNeed>
     */
    #[ORM\OneToMany(targetEntity: UserNeed::class, mappedBy: 'need')]
    private Collection $userNeeds;

    /**
     * @var Collection<int, Feeling>
     */
    #[ORM\ManyToMany(targetEntity: Feeling::class, mappedBy: 'needs')]
    private Collection $feelings;

    #[ORM\Column(nullable: true)]
    private ?array $fulfilment = null;
 
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null;

    /**
     * @var Collection<int, Action>
     */
    #[ORM\ManyToMany(targetEntity: Action::class, inversedBy: 'needs')]
    private Collection $actions;

    public function __construct()
    {
        $this->userNeeds = new ArrayCollection();
        $this->feelings = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

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
            $userNeed->setNeed($this);
        }

        return $this;
    }

    public function removeUserNeed(UserNeed $userNeed): static
    {
        if ($this->userNeeds->removeElement($userNeed)) {
            // set the owning side to null (unless already changed)
            if ($userNeed->getNeed() === $this) {
                $userNeed->setNeed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feeling>
     */
    public function getFeelings(): Collection
    {
        return $this->feelings;
    }

    public function addFeeling(Feeling $feeling): static
    {
        if (!$this->feelings->contains($feeling)) {
            $this->feelings->add($feeling);
            $feeling->addNeed($this);
        }

        return $this;
    }
    
    public function removeFeeling(Feeling $feeling): static
    {
        if ($this->feelings->removeElement($feeling)) {
            $feeling->removeNeed($this);
        }

        return $this;
    }

    public function getFulfilment(): ?array
    {
        return $this->fulfilment;
    }

    public function setFulfilment(?array $fulfilment): static
    {
        $this->fulfilment = $fulfilment;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }

        return $this;
    }

    public function removeAction(Action $action): static
    {
        $this->actions->removeElement($action);

        return $this;
    }
}
