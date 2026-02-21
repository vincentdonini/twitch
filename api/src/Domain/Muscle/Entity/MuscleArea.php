<?php

namespace App\Domain\Muscle\Entity;

use App\Domain\Content\Entity\ContentMuscleArea;
use App\Infrastructure\Doctrine\Repository\Muscle\MuscleAreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MuscleAreaRepository::class)]
#[ORM\Table(name: 'muscle_area')]
class MuscleArea
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "Muscle area ID")]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "Muscle area slug", example: "arms")]
    private string $slug;

    #[ORM\OneToMany(targetEntity: MuscleGroup::class, mappedBy: 'muscleArea', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $muscleGroups;

    #[ORM\OneToMany(targetEntity: ContentMuscleArea::class, mappedBy: "muscleArea", cascade: ["persist", "remove"])]
    private Collection $contents;

    public function __construct(
        string $slug,
    ) {
        $this->id = Uuid::v7();

        $this->slug = $slug;

        $this->muscleGroups = new ArrayCollection();
        $this->contents     = new ArrayCollection();
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
    // MUSCLE GROUPS
    // -----------------------------------------------------------------------------------------------------------------
    public function getMuscleGroups(): Collection
    {
        return $this->muscleGroups;
    }

    public function addMuscleGroup(MuscleGroup $group): self
    {
        if (!$this->muscleGroups->contains($group)) {
            $this->muscleGroups->add($group);
            $group->setMuscleArea($this);
        }
        return $this;
    }

    public function removeMuscleGroup(MuscleGroup $group): self
    {
        $this->muscleGroups->removeElement($group);
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentMuscleArea $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setMuscleArea($this);
        }
        return $this;
    }

    public function removeContent(ContentMuscleArea $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentMuscleArea
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
