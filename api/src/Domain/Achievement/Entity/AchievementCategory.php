<?php

namespace App\Domain\Achievement\Entity;

use App\Domain\Content\Entity\ContentAchievementCategory;
use App\Infrastructure\Doctrine\Repository\Achievement\AchievementCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AchievementCategoryRepository::class)]
#[ORM\Table(name: 'achievement_category')]
class AchievementCategory
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $code;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\OneToMany(
        targetEntity: AchievementGroup::class,
        mappedBy    : 'category',
        cascade     : ['persist', 'remove']
    )]
    private Collection $achievementGroups;

    #[ORM\OneToMany(targetEntity: ContentAchievementCategory::class, mappedBy: "achievementCategory", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string $code,
        int    $position,
    ) {
        $this->id = Uuid::v7();

        $this->code     = $code;
        $this->position = $position;

        $this->achievementGroups = new ArrayCollection();
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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GROUPS
    // -----------------------------------------------------------------------------------------------------------------

    public function getAchievementGroups(): Collection
    {
        return $this->achievementGroups;
    }

    public function addAchievementGroup(AchievementGroup $achievementGroup): self
    {
        if (!$this->achievementGroups->contains($achievementGroup)) {
            $this->achievementGroups[] = $achievementGroup;
            $achievementGroup->setAchievementCategory($this);
        }

        return $this;
    }

    public function removeAchievementGroup(AchievementGroup $achievementGroup): self
    {
        if ($this->achievementGroups->contains($achievementGroup)) {
            $this->achievementGroups->removeElement($achievementGroup);
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

    public function addContent(ContentAchievementCategory $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setAchievementCategory($this);
        }
        return $this;
    }

    public function removeContent(ContentAchievementCategory $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentAchievementCategory
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
