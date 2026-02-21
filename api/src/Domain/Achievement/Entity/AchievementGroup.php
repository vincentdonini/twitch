<?php

namespace App\Domain\Achievement\Entity;

use App\Domain\Content\Entity\ContentAchievementGroup;
use App\Infrastructure\Doctrine\Repository\Achievement\AchievementGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AchievementGroupRepository::class)]
#[ORM\Table(name: 'achievement_group')]
class AchievementGroup
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $code;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\ManyToOne(targetEntity: AchievementCategory::class, inversedBy: 'achievementGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private AchievementCategory $achievementCategory;

    #[ORM\OneToMany(
        targetEntity : Achievement::class,
        mappedBy     : 'group',
        cascade      : ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $achievements;

    #[ORM\OneToMany(targetEntity: ContentAchievementGroup::class, mappedBy: "achievementGroup", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string              $code,
        int                 $position,
        AchievementCategory $achievementCategory,
    ) {
        $this->id = Uuid::v7();

        $this->code                = $code;
        $this->position            = $position;
        $this->achievementCategory = $achievementCategory;

        $this->achievements = new ArrayCollection();
        $this->contents     = new ArrayCollection();
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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getAchievementCategory(): AchievementCategory
    {
        return $this->achievementCategory;
    }

    public function setAchievementCategory(AchievementCategory $achievementCategory): self
    {
        $this->achievementCategory = $achievementCategory;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // ACHIEVEMENTS
    // -----------------------------------------------------------------------------------------------------------------

    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

    public function addAchievement(Achievement $achievement): self
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements[] = $achievement;
            $achievement->setAchievementGroup($this);
        }

        return $this;
    }

    public function removeAchievement(Achievement $achievement): self
    {
        if ($this->achievements->contains($achievement)) {
            $this->achievements->removeElement($achievement);
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

    public function addContent(ContentAchievementGroup $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setAchievementGroup($this);
        }
        return $this;
    }

    public function removeContent(ContentAchievementGroup $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentAchievementGroup
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
