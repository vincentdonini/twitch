<?php

namespace App\Domain\Content\Entity;

use App\Domain\Wod\Entity\Wod;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class ContentWod
{
    #[ORM\Id, ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Wod::class, inversedBy: "contents")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private Wod $wod;

    #[ORM\Column(type: "string", length: 5)]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private string $locale;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private string $title;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private string $summary;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private ?string $details = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private ?string $rules = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups([FrontGroupsEnum::CONTENT_WOD_LIST])]
    private ?string $tips = null;

    public function __construct(
        Wod     $wod,
        string  $locale,
        string  $title,
        string  $summary,
        ?string $details = null,
        ?string $rules = null,
        ?string $tips = null,
    ) {
        $this->wod     = $wod;
        $this->locale  = $locale;
        $this->title   = $title;
        $this->summary = $summary;
        $this->details = $details;
        $this->rules   = $rules;
        $this->tips    = $tips;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getWod(): Wod
    {
        return $this->wod;
    }

    public function setWod(Wod $wod): self
    {
        $this->wod = $wod;
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

    public function getRules(): ?string
    {
        return $this->rules;
    }

    public function setRules(?string $rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    public function getTips(): ?string
    {
        return $this->tips;
    }

    public function setTips(?string $tips): self
    {
        $this->tips = $tips;
        return $this;
    }
}
