<?php

namespace App\Domain\Exercise\Entity;

use App\Domain\Content\Entity\ContentExerciseCategory;
use App\Infrastructure\Doctrine\Repository\Exercise\ExerciseCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: ExerciseCategoryRepository::class)]
#[ORM\Table(name: 'exercise_category')]
class ExerciseCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "Exercise category ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "Exercise category slug", example: "warm-up")]
    private string $slug;

    #[ORM\OneToMany(targetEntity: ContentExerciseCategory::class, mappedBy: "movementCategory", cascade: ["persist", "remove"], orphanRemoval: true)]
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

    public function addContent(ContentExerciseCategory $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setExerciseCategory($this);
        }
        return $this;
    }

    public function removeContent(ContentExerciseCategory $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentExerciseCategory
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
