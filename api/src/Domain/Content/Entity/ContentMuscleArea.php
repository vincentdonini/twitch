<?php

namespace App\Domain\Content\Entity;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class ContentMuscleArea
{
    #[ORM\Id, ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups([FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class, inversedBy: "contents")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups([FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST])]
    private MuscleArea $muscleArea;

    #[ORM\Column(type: "string", length: 5)]
    #[Groups([FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST])]
    private string $locale;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups([FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST])]
    private string $title;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups([FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST])]
    private string $summary;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups([FrontGroupsEnum::CONTENT_MUSCLE_AREA_LIST])]
    private ?string $details = null;

    public function __construct(
        MuscleArea $muscleArea,
        string     $locale,
        string     $title,
        string     $summary,
        ?string    $details = null
    ) {
        $this->muscleArea = $muscleArea;
        $this->locale     = $locale;
        $this->title      = $title;
        $this->summary    = $summary;
        $this->details    = $details;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMuscleArea(): MuscleArea
    {
        return $this->muscleArea;
    }

    public function setMuscleArea(MuscleArea $muscleArea): self
    {
        $this->muscleArea = $muscleArea;
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

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;
        return $this;
    }
}
