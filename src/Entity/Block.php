<?php

namespace App\Entity;

use App\Repository\BlockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlockRepository::class)]
class Block
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?array $beliefs = null;

    #[ORM\Column(nullable: true)]
    private ?array $reframings = null;

    /**
     * @var Collection<int, Action>
     */
    #[ORM\ManyToMany(targetEntity: Action::class, mappedBy: 'blocks')]
    private Collection $actions;

    /**
     * @var Collection<int, Feeling>
     */
    #[ORM\ManyToMany(targetEntity: Feeling::class, mappedBy: 'blocks')]
    private Collection $feelings;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
        $this->feelings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
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

    public function getBeliefs(): ?array
    {
        return $this->beliefs;
    }

    public function setBeliefs(?array $beliefs): static
    {
        $this->beliefs = $beliefs;

        return $this;
    }

    public function getReframings(): ?array
    {
        return $this->reframings;
    }

    public function setReframings(?array $reframings): static
    {
        $this->reframings = $reframings;

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
            $action->addBlock($this);
        }

        return $this;
    }

    public function removeAction(Action $action): static
    {
        if ($this->actions->removeElement($action)) {
            $action->removeBlock($this);
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
            $feeling->addBlock($this);
        }

        return $this;
    }

    public function removeFeeling(Feeling $feeling): static
    {
        if ($this->feelings->removeElement($feeling)) {
            $feeling->removeBlock($this);
        }

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
}
