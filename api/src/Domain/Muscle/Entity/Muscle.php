<?php

namespace App\Domain\Muscle\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Content\Entity\ContentMuscle;
use App\Domain\Movement\Entity\Movement;
use App\Infrastructure\ApiPlatform\Muscle\Provider\MuscleProvider;
use App\Infrastructure\Persistence\Doctrine\Muscle\MuscleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: MuscleRepository::class)]
#[ORM\Table(name: 'muscles')]
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
        'groups' => [
            'muscle:list',
        ],
    ],
    provider            : MuscleProvider::class
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'muscle:detail',
        ],
    ],
    provider            : MuscleProvider::class,
)]
class Muscle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
        'wod:list', 'wod:detail',
    ])]
    #[OA\Property(description: "Muscle ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
        'wod:list', 'wod:detail',
    ])]
    #[OA\Property(description: "Muscle slug", example: "upper-pectoralis")]
    private string $slug;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
        'wod:list', 'wod:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name = null;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
        'wod:list', 'wod:detail',
    ])]
    #[SerializedName('summary')]
    private string $summary;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
        'wod:list', 'wod:detail',
    ])]
    #[SerializedName('details')]
    private string $details;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
        'wod:list', 'wod:detail',
    ])]
    private MuscleArea $area;

    #[ORM\ManyToOne(targetEntity: MuscleGroup::class)]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
        'wod:list', 'wod:detail',
    ])]
    private ?MuscleGroup $group = null;

    #[ORM\ManyToMany(targetEntity: Movement::class, mappedBy: 'muscles')]
    #[Groups(['muscle:detail'])]
    private Collection $movements;

    #[ORM\OneToMany(targetEntity: ContentMuscle::class, mappedBy: "muscle", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
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

    public function getGroup(): ?MuscleGroup
    {
        return $this->group;
    }

    public function setGroup(?MuscleGroup $group): self
    {
        $this->group = $group;
        return $this;
    }

    // MUSCLES
    // -----------------------------------------------------------------------------------------------------------------
    public function getMovements(): Collection
    {
        return $this->movements;
    }

    public function addMovement(Movement $movement): self
    {
        if (!$this->movements->contains($movement)) {
            $this->movements->add($movement);
            $movement->addMuscle($this);
        }
        return $this;
    }

    public function removeMovement(Movement $movement): self
    {
        if ($this->movements->removeElement($movement)) {
            $movement->removeMuscle($this);
        }
        return $this;
    }

    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentMuscle $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setMuscle($this);
        }
        return $this;
    }

    public function removeContent(ContentMuscle $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentMuscle
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
