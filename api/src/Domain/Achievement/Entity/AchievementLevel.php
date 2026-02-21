<?php

namespace App\Domain\Achievement\Entity;

use App\Domain\Achievement\Enum\AchievementLevelEnum;
use App\Infrastructure\Doctrine\Repository\Achievement\AchievementLevelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AchievementLevelRepository::class)]
#[ORM\Table(name: 'achievement_level')]
class AchievementLevel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', enumType: AchievementLevelEnum::class)]
    private AchievementLevelEnum $level;

    #[ORM\Column(type: 'integer')]
    private int $unlockCount = 0;

    #[ORM\ManyToOne(targetEntity: Achievement::class, inversedBy: 'levels')]
    #[ORM\JoinColumn(nullable: false)]
    private Achievement $achievement;

    public function __construct(
        AchievementLevelEnum $level,
        Achievement          $achievement,
    ) {
        $this->id = Uuid::v7();

        $this->level       = $level;
        $this->achievement = $achievement;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLevel(): AchievementLevelEnum
    {
        return $this->level;
    }

    public function setLevel(AchievementLevelEnum $level): self
    {
        $this->level = $level;
        return $this;
    }

    public function getUnlockCount(): int
    {
        return $this->unlockCount;
    }

    public function setUnlockCount(int $unlockCount): self
    {
        $this->unlockCount = $unlockCount;
        return $this;
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
}
