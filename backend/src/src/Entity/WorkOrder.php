<?php

namespace App\Entity;

use App\Repository\WorkOrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkOrderRepository::class)]
#[ORM\Table(name: 'work_order')]
class WorkOrder
{
    // Estados definidos (strings consistentes, sin espacios)
    public const STATUS_PENDING = 'PENDIENTE';
    public const STATUS_IN_PROGRESS = 'EN_CURSO';
    public const STATUS_DONE = 'FINALIZADA';

    // Si el cliente cambia/define nuevos estados, se agregan aquí
    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_DONE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private string $title = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 30)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $assignedTo = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = trim($title);
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description !== null ? trim($description) : null;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $status = strtoupper(trim($status));

        if ($status === '') {
            $status = self::STATUS_PENDING;
        }

        if (!in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException('Invalid status: ' . $status);
        }

        $this->status = $status;
        return $this;
    }

    public function advanceStatus(): self
    {
        // Flujo simple de avance
        if ($this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_IN_PROGRESS;
        } elseif ($this->status === self::STATUS_IN_PROGRESS) {
            $this->status = self::STATUS_DONE;
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }
}