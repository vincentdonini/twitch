<?php

namespace App\Domain\Muscle\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
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
    provider: MuscleProvider::class
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'muscle:detail',
        ],
    ],
    provider: MuscleProvider::class,
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
    ])]
    #[OA\Property(description: "Muscle ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
    ])]
    #[OA\Property(description: "Muscle slug", example: "upper-pectoralis")]
    private string $slug;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name = null;

    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('description')]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: MuscleArea::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
    ])]
    private MuscleArea $area;

    #[ORM\ManyToOne(targetEntity: MuscleGroup::class)]
    #[Groups([
        'muscle:list', 'muscle:detail',
        'movement:list', 'movement:detail',
    ])]
    private ?MuscleGroup $group = null;

    #[ORM\ManyToMany(targetEntity: Movement::class, mappedBy: 'muscles')]
    #[Groups(['muscle:detail'])]
    private Collection $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
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
}
