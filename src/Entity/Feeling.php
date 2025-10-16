<?php

namespace App\Entity;

use App\Repository\FeelingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeelingRepository::class)]
class Feeling
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
    private ?string $emotion = null;

    #[ORM\Column(nullable: true)]
    private ?array $triggers = null;

    /**
     * @var Collection<int, Block>
     */
    #[ORM\ManyToMany(targetEntity: Block::class, inversedBy: 'feelings')]
    private Collection $blocks;

    /**
     * @var Collection<int, Need>
     */
    #[ORM\ManyToMany(targetEntity: Need::class, inversedBy: 'feelings')]
    private Collection $needs;

    #[ORM\Column(length: 25)]
    private ?string $color = null;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->needs = new ArrayCollection();
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

    public function getEmotion(): ?string
    {
        return $this->emotion;
    }

    public function setEmotion(?string $emotion): static
    {
        $this->emotion = $emotion;

        return $this;
    }

    public function getTriggers(): ?array
    {
        return $this->triggers;
    }

    public function setTriggers(?array $triggers): static
    {
        $this->triggers = $triggers;

        return $this;
    }

    /**
     * @return Collection<int, Block>
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(Block $block): static
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
        }

        return $this;
    }

    public function removeBlock(Block $block): static
    {
        $this->blocks->removeElement($block);

        return $this;
    }

    /**
     * @return Collection<int, Need>
     */
    public function getNeeds(): Collection
    {
        return $this->needs;
    }

    public function addNeed(Need $need): static
    {
        if (!$this->needs->contains($need)) {
            $this->needs->add($need);
        }

        return $this;
    }

    public function removeNeed(Need $need): static
    {
        $this->needs->removeElement($need);

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
