<?php

namespace App\Domain\Equipment\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\ApiPlatform\Equipment\Provider\EquipmentProvider;
use App\Infrastructure\Persistence\Doctrine\Equipment\EquipmentRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\Table(name: 'equipments')]
#[ApiResource(
    formats                      : ['json', 'jsonapi'],
    paginationClientEnabled      : false,
    paginationClientItemsPerPage : true,
    paginationEnabled            : true,
    paginationItemsPerPage       : 10,
    paginationMaximumItemsPerPage: 50,
)]
#[GetCollection(
    uriTemplate         : '/equipments',
    normalizationContext: [
        'groups' => [
            'equipment:list',
        ],
    ],
    provider            : EquipmentProvider::class,
)]
#[Get(
    uriTemplate         : '/equipments/{id}',
    normalizationContext: [
        'groups' => [
            'equipment:detail',
        ],
    ],
    provider            : EquipmentProvider::class,
)]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    #[Groups([
        'wod:list', 'wod:detail',
        'equipment:list', 'equipment:detail',
        'movement:list', 'movement:detail',
    ])]
    #[OA\Property(description: "Equipment ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
        'equipment:list', 'equipment:detail',
        'movement:list', 'movement:detail',
    ])]
    #[OA\Property(description: "Equipment slug", example: "dip-bars")]
    private string $slug;

    #[Groups([
        'wod:list', 'wod:detail',
        'equipment:list', 'equipment:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('name')]
    private string $name;

    #[Groups([
        'wod:list', 'wod:detail',
        'equipment:list', 'equipment:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('description')]
    private string $description;

    public function __construct()
    {
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
