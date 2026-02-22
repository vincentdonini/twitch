<?php

namespace App\Domain\Wod\Entity;

use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Repository\Wod\WodScoreRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WodScoreRepository::class)]
#[ORM\Table(name: 'wod_score')]
class WodScore
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Wod::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Wod $wod;

    #[ORM\ManyToOne(targetEntity: WodVariant::class)]
    #[ORM\JoinColumn(nullable: false)]
    private WodVariant $wodVariant;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $time = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $repetitions = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $performedAt;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'boolean')]
    private bool $private;

    public function __construct(
        User              $user,
        Wod               $wod,
        WodVariant        $wodVariant,
        DateTimeImmutable $performedAt,
        bool              $private = false,
    ) {
        $this->id = Uuid::v7();

        $this->user        = $user;
        $this->wod         = $wod;
        $this->wodVariant  = $wodVariant;
        $this->performedAt = $performedAt;
        $this->private     = $private;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getWod(): Wod
    {
        return $this->wod;
    }

    public function getWodVariant(): WodVariant
    {
        return $this->wodVariant;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;
        return $this;
    }

    public function getRepetitions(): ?int
    {
        return $this->repetitions;
    }

    public function setRepetitions(?int $repetitions): self
    {
        $this->repetitions = $repetitions;
        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getPerformedAt(): DateTimeImmutable
    {
        return $this->performedAt;
    }

    public function setPerformedAt(DateTimeImmutable $performedAt): self
    {
        $this->performedAt = $performedAt;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setIsPrivate(bool $private): self
    {
        $this->private = $private;
        return $this;
    }

    public function assertScoreIsValid(): void
    {
        $allowed = $this->getWodVariant()->getWod()->getWodType()->getAllowedMetrics();

        $metrics = [
            'time'        => $this->time,
            'repetitions' => $this->repetitions,
            'weight'      => $this->weight,
        ];

        foreach ($metrics as $metric => $value) {
            if ($value !== null && !in_array($metric, $allowed, true)) {
                throw new DomainException();
            }
        }

        foreach ($allowed as $metric) {
            if ($this->{$metric} === null) {
                throw new DomainException();
            }
        }
    }
}
