<?php

namespace App\Domain\Achievement\Entity;

use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Repository\Achievement\UserAchievementProgressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAchievementProgressRepository::class)]
#[ORM\Table(
    name             : 'user_achievement_progress',
    indexes          : [
        new ORM\Index(columns: ['completed']),
        new ORM\Index(columns: ['user_id']),
    ],
    uniqueConstraints: [
        new ORM\UniqueConstraint(columns: ['user_id', 'achievement_id', 'achievement_level_id']),
    ]
)]
class UserAchievementProgress
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Achievement::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Achievement $achievement;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: AchievementLevel::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AchievementLevel $achievementLevel;

    #[ORM\Column(type: 'float')]
    private float $progress;

    #[ORM\Column(type: 'boolean')]
    private bool $completed;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    public function __construct(
        User             $user,
        Achievement      $achievement,
        AchievementLevel $achievementLevel,
    ) {
        $this->user             = $user;
        $this->achievement      = $achievement;
        $this->achievementLevel = $achievementLevel;
        $this->progress         = 0.0;
        $this->completed        = false;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAchievement(): Achievement
    {
        return $this->achievement;
    }

    public function setAchievement(Achievement $achievement): self
    {
        $this->achievement = $achievement;
        return $this;
    }

    public function getAchievementLevel(): AchievementLevel
    {
        return $this->achievementLevel;
    }

    public function setAchievementLevel(AchievementLevel $achievementLevel): self
    {
        $this->achievementLevel = $achievementLevel;
        return $this;
    }

    public function getProgress(): float
    {
        return $this->progress;
    }

    public function setProgress(float $progress): self
    {
        $this->progress = $progress;
        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // BUSINESS
    // -----------------------------------------------------------------------------------------------------------------

    public function updateProgress(float $progress): self
    {
        $this->progress = max(0.0, min(100.0, $progress));

        if ($this->progress >= 100.0) {
            if (!$this->completed) {
                $this->markAsCompleted();
            }
        } else {
            if ($this->completed) {
                $this->unmarkAsCompleted();
            }
        }

        return $this;
    }

    public function markAsCompleted(): self
    {
        $this->completed   = true;
        $this->progress    = 100.0;
        $this->completedAt = new \DateTimeImmutable();

        return $this;
    }

    public function unmarkAsCompleted(): self
    {
        $this->completed   = false;
        $this->completedAt = null;

        return $this;
    }
}
