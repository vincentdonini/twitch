<?php

namespace App\Domain\Muscle\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Domain\Content\Entity\ContentMuscleGroup;
use App\Infrastructure\ApiPlatform\Muscle\Provider\MuscleGroupMusclesProvider;
use App\Infrastructure\ApiPlatform\Muscle\Provider\MuscleGroupProvider;
use App\Infrastructure\Persistence\Doctrine\Muscle\MuscleGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: MuscleGroupRepository::class)]
#[ORM\Table(name: 'muscle_groups')]
#[ApiResource(
    formats: ['json', 'jsonapi'],
    paginationClientEnabled: false,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 50,
)]
#[GetCollection(
    normalizationContext: ['groups' => ['muscleGroup:list']],
    provider: MuscleGroupProvider::class
)]
#[Get(
    normalizationContext: ['groups' => ['muscleGroup:detail']],
    provider: MuscleGroupProvider::class
)]
#[GetCollection(
    uriTemplate: '/muscle-groups/{id}/muscles',
    uriVariables: [
        'id' => new Link(fromProperty: 'muscles', fromClass: MuscleGroup::class),
    ],
    normalizationContext: ['groups' => ['muscleGroup:list','muscle:list']],
    provider: MuscleGroupMusclesProvider::class
)]
class MuscleGroup
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
    #[OA\Property(description: "Muscle group ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'muscle:list','muscle:detail',
        'muscleArea:list','muscleArea:detail',
        'muscleGroup:list','muscleGroup:detail',
        'movement:list','movement:detail',
    ])]
    #[OA\Property(description: "Muscle group slug", example: "biceps")]
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

    #[ORM\ManyToOne(targetEntity: MuscleArea::class, inversedBy: 'muscleGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private MuscleArea $area;

    #[ORM\OneToMany(targetEntity: Muscle::class, mappedBy: 'group', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $muscles;

    #[ORM\OneToMany(targetEntity: ContentMuscleGroup::class, mappedBy: "muscleGroup", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct()
    {
        $this->muscles = new ArrayCollection();
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

    public function getArea(): MuscleArea
    {
        return $this->area;
    }

    public function setArea(MuscleArea $area): self
    {
        $this->area = $area;
        return $this;
    }

    // MUSCLES
    // -----------------------------------------------------------------------------------------------------------------
    public function getMuscles(): Collection
    {
        return $this->muscles;
    }

    public function addMuscle(Muscle $muscle): self
    {
        if (!$this->muscles->contains($muscle)) {
            $this->muscles->add($muscle);
            $muscle->setGroup($this);
        }
        return $this;
    }

    public function removeMuscle(Muscle $muscle): self
    {
        if ($this->muscles->removeElement($muscle)) {
            if ($muscle->getGroup() === $this) {
                $muscle->setGroup(null);
            }
        }
        return $this;
    }

    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentMuscleGroup $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setMuscleGroup($this);
        }
        return $this;
    }

    public function removeContent(ContentMuscleGroup $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentMuscleGroup
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
