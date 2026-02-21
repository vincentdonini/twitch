<?php

namespace App\Domain\Achievement\Entity;

use App\Domain\Achievement\Enum\AchievementSourceEnum;
use App\Domain\Content\Entity\ContentAchievement;
use App\Infrastructure\Doctrine\Repository\Achievement\AchievementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AchievementRepository::class)]
#[ORM\Table(name: 'achievement')]
class Achievement
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', enumType: AchievementSourceEnum::class)]
    private AchievementSourceEnum $source;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\ManyToOne(targetEntity: AchievementGroup::class, inversedBy: 'achievements')]
    #[ORM\JoinColumn(nullable: false)]
    private AchievementGroup $achievementGroup;

    #[ORM\OneToMany(
        targetEntity : AchievementLevel::class,
        mappedBy     : 'achievement',
        cascade      : ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $achievementLevels;

    #[ORM\OneToMany(targetEntity: ContentAchievement::class, mappedBy: "achievement", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string                $code,
        int                   $position,
        AchievementSourceEnum $source,
        AchievementGroup      $achievementGroup,
    ) {
        $this->id = Uuid::v7();

        $this->code             = $code;
        $this->position         = $position;
        $this->source           = $source;
        $this->achievementGroup = $achievementGroup;

        $this->achievementLevels = new ArrayCollection();
        $this->contents          = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getSource(): AchievementSourceEnum
    {
        return $this->source;
    }

    public function setSource(AchievementSourceEnum $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getAchievementGroup(): AchievementGroup
    {
        return $this->achievementGroup;
    }

    public function setAchievementGroup(AchievementGroup $achievementGroup): self
    {
        $this->achievementGroup = $achievementGroup;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // LEVELS
    // -----------------------------------------------------------------------------------------------------------------

    public function getAchievementLevels(): Collection
    {
        return $this->achievementLevels;
    }

    public function addAchievementLevel(AchievementLevel $achievementLevel): self
    {
        if (!$this->achievementLevels->contains($achievementLevel)) {
            $this->achievementLevels[] = $achievementLevel;
            $achievementLevel->setAchievement($this);
        }

        return $this;
    }

    public function removeAchievementLevel(AchievementLevel $achievementLevel): self
    {
        if ($this->achievementLevels->contains($achievementLevel)) {
            $this->achievementLevels->removeElement($achievementLevel);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------

    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentAchievement $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setAchievement($this);
        }
        return $this;
    }

    public function removeContent(ContentAchievement $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentAchievement
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
