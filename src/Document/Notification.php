<?php

namespace App\Document;
use App\Repository\NotificationRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'notifications', repositoryClass: NotificationRepository::class)]
#[ODM\Index(keys: ['createdAt' => 'desc'])]
class Notification
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'int')]
    private int $userId;

    #[ODM\Field(type: 'int', nullable: true)]
    private ?int $userActionId = null;

    #[ODM\Field(type: 'string')]
    private string $title;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $message = null;

    #[ODM\Field(type: 'string')]
    private string $type;

    #[ODM\Field(type: 'bool')]
    private bool $isRead = false;

    #[ODM\Field(type: 'date_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ODM\Field(type: 'hash')]
    private array $context = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserActionId(): ?int
    {
        return $this->userActionId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getContext(): array
    {
        return $this->context;
    }
    
    public function setUserId(int $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUserActionId(?int $userActionId): static
    {
        $this->userActionId = $userActionId;
        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
        return $this;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setContext(array $context): static
    {
        $this->context = $context;
        return $this;
    }
}
