<?php

namespace App\Domain\Wod\Entity;

use App\Domain\Content\Entity\ContentWodCategory;
use App\Infrastructure\Doctrine\Repository\Wod\WodCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WodCategoryRepository::class)]
#[ORM\Table(name: 'wod_category')]
class WodCategory
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    private string $slug;

    #[ORM\OneToMany(targetEntity: ContentWodCategory::class, mappedBy: "wodCategory", cascade: ["persist", "remove"], orphanRemoval: true)]
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
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentWodCategory $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setWodCategory($this);
        }
        return $this;
    }

    public function removeContent(ContentWodCategory $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentWodCategory
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
