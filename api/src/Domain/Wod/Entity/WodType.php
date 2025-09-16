<?php

namespace App\Domain\Wod\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\ApiPlatform\Wod\Provider\WodTypeProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: WodTypeRepository::class)]
#[ORM\Table(name: 'wod_types')]
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
            'wodType:list',
        ],
    ],
    provider            : WodTypeProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'wodType:detail',
        ],
    ],
    provider            : WodTypeProvider::class,
)]
class WodType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodType:list', 'wodType:detail',
    ])]
    #[OA\Property(description: "WOD Type ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodType:list', 'wodType:detail',
    ])]
    #[OA\Property(description: "WOD type slug", example: "for-load")]
    private string $slug;

    #[Groups([
        'wod:list', 'wod:detail',
        'wodType:list', 'wodType:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name = null;

    #[Groups([
        'wod:list', 'wod:detail',
        'wodType:list', 'wodType:detail',
    ])]
    #[SerializedName('description')]
    private ?string $description = null;

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
}
