<?php

namespace App\Domain\Muscle\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Domain\Content\Entity\ContentMuscleArea;
use App\Infrastructure\ApiPlatform\Muscle\Provider\MuscleAreaGroupMusclesProvider;
use App\Infrastructure\ApiPlatform\Muscle\Provider\MuscleAreaGroupProvider;
use App\Infrastructure\ApiPlatform\Muscle\Provider\MuscleAreaProvider;
use App\Infrastructure\Persistence\Doctrine\Muscle\MuscleAreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: MuscleAreaRepository::class)]
#[ORM\Table(name: 'muscle_areas')]
#[ApiResource(
    formats                      : ['json', 'jsonapi'],
    paginationClientEnabled      : false,
    paginationClientItemsPerPage : true,
    paginationEnabled            : true,
    paginationItemsPerPage       : 10,
    paginationMaximumItemsPerPage: 50,
)]
#[GetCollection(
    normalizationContext: [
        'groups' => ['muscleArea:list'],
    ],
    provider: MuscleAreaProvider::class
)]
#[Get(
    normalizationContext: [
        'groups' => ['muscleArea:detail'],
    ],
    provider: MuscleAreaProvider::class,
)]
#[GetCollection(
    uriTemplate: '/muscle-areas/{areaId}/groups',
    uriVariables: [
        'areaId' => new Link(fromProperty: 'muscleGroups', fromClass: MuscleArea::class),
    ],
    normalizationContext: ['groups' => ['muscleArea:list','muscleGroup:list']],
    provider: MuscleAreaGroupProvider::class
)]
#[Get(
    uriTemplate: '/muscle-areas/{areaId}/groups/{groupId}',
    uriVariables: [
        'areaId' => new Link(fromProperty: 'muscleGroups', fromClass: MuscleArea::class),
        'groupId' => new Link(fromClass: MuscleGroup::class),
    ],
    normalizationContext: ['groups' => ['muscleArea:detail','muscleGroup:detail']],
    provider: MuscleAreaGroupProvider::class
)]
#[GetCollection(
    uriTemplate: '/muscle-areas/{areaId}/groups/{groupId}/muscles',
    uriVariables: [
        'areaId' => new Link(fromClass: MuscleArea::class),
        'groupId' => new Link(fromClass: MuscleGroup::class),
    ],
    normalizationContext: ['groups' => ['muscle:list']],
    provider: MuscleAreaGroupMusclesProvider::class
)]
class MuscleArea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    #[Groups([
        'muscle:list','muscle:detail',
        'muscleArea:list','muscleArea:detail',
        'muscleGroup:list','muscleGroup:detail',
        'movement:list','movement:detail',
    ])]
    #[OA\Property(description: "Muscle area ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'muscle:list','muscle:detail',
        'muscleArea:list','muscleArea:detail',
        'muscleGroup:list','muscleGroup:detail',
        'movement:list','movement:detail',
    ])]
    #[OA\Property(description: "Muscle area slug", example: "arms")]
    private string $slug;

    #[Groups([
        'muscle:list','muscle:detail',
        'muscleArea:list','muscleArea:detail',
        'muscleGroup:list','muscleGroup:detail',
        'movement:list','movement:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name = null;

    #[Groups([
        'muscle:list','muscle:detail',
        'muscleArea:list','muscleArea:detail',
        'muscleGroup:list','muscleGroup:detail',
        'movement:list','movement:detail',
    ])]
    #[SerializedName('summary')]
    private string $summary;

    #[Groups([
        'muscle:list','muscle:detail',
        'muscleArea:list','muscleArea:detail',
        'muscleGroup:list','muscleGroup:detail',
        'movement:list','movement:detail',
    ])]
    #[SerializedName('details')]
    private string $details;

    #[ORM\OneToMany(targetEntity: MuscleGroup::class, mappedBy: 'area', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $muscleGroups;

    #[ORM\OneToMany(targetEntity: ContentMuscleArea::class, mappedBy: "muscleArea", cascade: ["persist", "remove"])]
    private Collection $contents;

    public function __construct()
    {
        $this->muscleGroups = new ArrayCollection();
        $this->contents = new ArrayCollection();
    }

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    public function getDetails(): string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;
        return $this;
    }

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
            $group->setArea($this);
        }
        return $this;
    }

    public function removeMuscleGroup(MuscleGroup $group): self
    {
        $this->muscleGroups->removeElement($group);
        return $this;
    }

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
