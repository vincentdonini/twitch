<?php

namespace App\Domain\Equipment\Entity;

use App\Domain\Content\Entity\ContentEquipment;
use App\Infrastructure\Doctrine\Repository\Equipment\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\Table(name: 'equipment')]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "Equipment ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "Equipment slug", example: "dip-bars")]
    private string $slug;

    #[ORM\OneToMany(targetEntity: ContentEquipment::class, mappedBy: "equipment", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct()
    {
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

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentEquipment $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setEquipment($this);
        }
        return $this;
    }

    public function removeContent(ContentEquipment $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentEquipment
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
