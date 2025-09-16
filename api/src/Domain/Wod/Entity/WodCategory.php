<?php

namespace App\Domain\Wod\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\ApiPlatform\Wod\Provider\WodCategoryProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: WodCategoryRepository::class)]
#[ORM\Table(name: 'wod_categories')]
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
            'wodCategory:list',
        ],
    ],
    provider            : WodCategoryProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'wodCategory:detail',
        ],
    ],
    provider            : WodCategoryProvider::class,
)]
class WodCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodCategory:list', 'wodCategory:detail',
    ])]
    #[OA\Property(description: "WOD Category ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodCategory:list', 'wodCategory:detail',
    ])]
    #[OA\Property(description: "WOD category slug", example: "the-heroes")]
    private string $slug;

    #[Groups([
        'wod:list', 'wod:detail',
        'wodCategory:list', 'wodCategory:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name;

    #[Groups([
        'wod:list', 'wod:detail',
        'wodCategory:list', 'wodCategory:detail',
    ])]
    #[SerializedName('description')]
    private ?string $description;

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
