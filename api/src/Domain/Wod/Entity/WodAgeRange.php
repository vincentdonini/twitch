<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Content\Entity\ContentWodAgeRange;
use App\Infrastructure\Doctrine\Repository\Wod\WodAgeRangeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: WodAgeRangeRepository::class)]
#[ORM\Table(name: 'wod_age_range')]
class WodAgeRange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "WOD Age range ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "WOD Age range slug", example: "for-load")]
    private string $slug;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $minAge = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $maxAge = null;

    #[ORM\OneToMany(targetEntity: ContentWodAgeRange::class, mappedBy: "wodAgeRange", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string $slug,
    ) {
        $this->slug     = $slug;
        $this->contents = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getMinAge(): ?int
    {
        return $this->minAge;
    }

    public function setMinAge(?int $minAge): self
    {
        $this->minAge = $minAge;
        return $this;
    }

    public function getMaxAge(): ?int
    {
        return $this->maxAge;
    }

    public function setMaxAge(?int $maxAge): self
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentWodAgeRange $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setWodAgeRange($this);
        }
        return $this;
    }

    public function removeContent(ContentWodAgeRange $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentWodAgeRange
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}