<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Content\Entity\ContentWodType;
use App\Infrastructure\Doctrine\Repository\Wod\WodTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WodTypeRepository::class)]
#[ORM\Table(name: 'wod_type')]
class WodType
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    private string $slug;

    #[ORM\Column(type: 'json')]
    private array $allowedMetrics = [];

    #[ORM\OneToMany(targetEntity: ContentWodType::class, mappedBy: "wodType", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct(
        string $slug,
    ) {
        $this->id = Uuid::v7();

        $this->slug = $slug;

        $this->contents = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
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

    public function getAllowedMetrics(): array
    {
        return $this->allowedMetrics;
    }

    public function setAllowedMetrics(array $allowedMetrics): self
    {
        $this->allowedMetrics = $allowedMetrics;
        return $this;
    }

    public function allows(string $metric): bool
    {
        return in_array($metric, $this->allowedMetrics, true);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentWodType $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setWodType($this);
        }
        return $this;
    }

    public function removeContent(ContentWodType $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentWodType
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
