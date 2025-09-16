<?php

namespace App\Domain\Wod\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Movement\Entity\Movement;
use App\Infrastructure\ApiPlatform\Wod\Provider\WodVersionVariantMovementProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionVariantMovementRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WodVersionVariantMovementRepository::class)]
#[ORM\Table(name: 'wod_version_variant_movements')]
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
            'wodVersionVariantMovement:list',
        ],
    ],
    provider            : WodVersionVariantMovementProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'wodVersionVariantMovement:detail',
        ],
    ],
    provider            : WodVersionVariantMovementProvider::class,
)]
class WodVersionVariantMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariantMovement:list', 'wodVersionVariantMovement:detail',
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariantMovement:list', 'wodVersionVariantMovement:detail',
    ])]
    private int $position;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariantMovement:list', 'wodVersionVariantMovement:detail',
    ])]
    private ?int $reps = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariantMovement:list', 'wodVersionVariantMovement:detail',
    ])]
    private ?float $weight = null;

    #[ORM\ManyToOne(targetEntity: WodVersionVariant::class, fetch: 'EAGER', inversedBy: 'wodVersionVariantMovements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wodVersionVariantMovement:list', 'wodVersionVariantMovement:detail',
    ])]
    private WodVersionVariant $wodVersionVariant;

    #[ORM\ManyToOne(targetEntity: Movement::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariantMovement:list', 'wodVersionVariantMovement:detail',
    ])]
    private Movement $movement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getReps(): ?int
    {
        return $this->reps;
    }

    public function setReps(?int $reps): self
    {
        $this->reps = $reps;
        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getWodVersionVariant(): WodVersionVariant
    {
        return $this->wodVersionVariant;
    }

    public function setWodVersionVariant(WodVersionVariant $variant): self
    {
        $this->wodVersionVariant = $variant;
        return $this;
    }

    public function getMovement(): Movement
    {
        return $this->movement;
    }

    public function setMovement(Movement $movement): self
    {
        $this->movement = $movement;
        return $this;
    }
}
