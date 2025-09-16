<?php

namespace App\Domain\Movement\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Muscle\Entity\Muscle;
use App\Infrastructure\ApiPlatform\Movement\Provider\MovementProvider;
use App\Infrastructure\Persistence\Doctrine\Movement\MovementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: MovementRepository::class)]
#[ORM\Table(name: 'movements')]
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
            'movement:list',
        ],
    ],
    provider: MovementProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'movement:detail',
        ],
    ],
    provider: MovementProvider::class,
)]
class Movement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
    ])]
    private string $slug;

    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name = null;

    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('description')]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Equipment::class, fetch: 'EAGER')]
    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
    ])]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne(targetEntity: MovementCategory::class, fetch: 'EAGER')]
    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
    ])]
    private MovementCategory $movementCategory;

    #[ORM\ManyToMany(targetEntity: Muscle::class)]
    #[ORM\JoinTable(
        name: 'movement_muscles',
        joinColumns: [new ORM\JoinColumn(name: 'movement_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'muscle_id', referencedColumnName: 'id')]
    )]
    #[Groups(['movement:list', 'movement:detail'])]
    private Collection $muscles;

    public function __construct()
    {
        $this->muscles = new ArrayCollection();
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

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;
        return $this;
    }

    public function getMovementCategory(): MovementCategory
    {
        return $this->movementCategory;
    }

    public function setMovementCategory(MovementCategory $movementCategory): self
    {
        $this->movementCategory = $movementCategory;
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
        }
        return $this;
    }

    public function removeMuscle(Muscle $muscle): self
    {
        $this->muscles->removeElement($muscle);
        return $this;
    }
}
