<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Content\Entity\ContentWodDivision;
use App\Infrastructure\Doctrine\Repository\Wod\WodDivisionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: WodDivisionRepository::class)]
#[ORM\Table(name: 'wod_division')]
class WodDivision
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "WOD division ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "WOD division slug", example: "rx")]
    private string $slug;

    #[ORM\OneToMany(targetEntity: WodVariant::class, mappedBy: 'wodDivision')]
    private Collection $wodVariants;

    #[ORM\OneToMany(targetEntity: ContentWodDivision::class, mappedBy: "wodDivision", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct()
    {
        $this->wodVariants = new ArrayCollection();
        $this->contents    = new ArrayCollection();
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

    // -----------------------------------------------------------------------------------------------------------------
    // WOD VARIANTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getWodVariants(): Collection
    {
        return $this->wodVariants;
    }

    public function addWodVariant(WodVariant $wodVariant): self
    {
        if (!$this->wodVariants->contains($wodVariant)) {
            $this->wodVariants[] = $wodVariant;
            $wodVariant->setWodDivision($this);
        }

        return $this;
    }

    public function removeWodVariant(WodVariant $wodVariant): self
    {
        if ($this->wodVariants->contains($wodVariant)) {
            $this->wodVariants->removeElement($wodVariant);
            // set the owning side to null (unless already changed)
            if ($wodVariant->getWodDivision() === $this) {
                $wodVariant->setWodDivision(null);
            }
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

    public function addContent(ContentWodDivision $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setWodDivision($this);
        }
        return $this;
    }

    public function removeContent(ContentWodDivision $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentWodDivision
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
