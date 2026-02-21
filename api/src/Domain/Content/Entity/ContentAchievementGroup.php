<?php

namespace App\Domain\Content\Entity;

use App\Domain\Achievement\Entity\AchievementGroup;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class ContentAchievementGroup
{
    #[ORM\Id, ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups([FrontGroupsEnum::CONTENT_ACHIEVEMENT_GROUP_LIST])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: AchievementGroup::class, inversedBy: "contents")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups([FrontGroupsEnum::CONTENT_ACHIEVEMENT_GROUP_LIST])]
    private AchievementGroup $achievementGroup;

    #[ORM\Column(type: "string", length: 5)]
    #[Groups([FrontGroupsEnum::CONTENT_ACHIEVEMENT_GROUP_LIST])]
    private string $locale;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups([FrontGroupsEnum::CONTENT_ACHIEVEMENT_GROUP_LIST])]
    private string $title;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups([FrontGroupsEnum::CONTENT_ACHIEVEMENT_GROUP_LIST])]
    private string $description;

    public function __construct(
        AchievementGroup $achievementGroup,
        string           $locale,
        string           $title,
        string           $description,
    ) {
        $this->achievementGroup = $achievementGroup;
        $this->locale           = $locale;
        $this->title            = $title;
        $this->description      = $description;
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
