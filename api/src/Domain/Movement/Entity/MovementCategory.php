<?php

namespace App\Domain\Movement\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Content\Entity\ContentMovementCategory;
use App\Infrastructure\ApiPlatform\Movement\Provider\MovementCategoryProvider;
use App\Infrastructure\Persistence\Doctrine\Movement\MovementCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: MovementCategoryRepository::class)]
#[ORM\Table(name: 'movement_categories')]
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
            'movementCategory:list',
        ],
    ],
    provider            : MovementCategoryProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'movementCategory:detail',
        ],
    ],
    provider            : MovementCategoryProvider::class,
)]
class MovementCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
        'movementCategory:list', 'movementCategory:detail',
    ])]
    #[OA\Property(description: "Movement category ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
        'movementCategory:list', 'movementCategory:detail',
    ])]
    #[OA\Property(description: "Movement category slug", example: "warm-up")]
    private string $slug;

    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
        'movementCategory:list', 'movementCategory:detail',
    ])]
    #[SerializedName('name')]
    private ?string $name = null;

    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
        'movementCategory:list', 'movementCategory:detail',
    ])]
    #[SerializedName('summary')]
    private string $summary;

    #[Groups([
        'wod:list', 'wod:detail',
        'movement:list', 'movement:detail',
        'movementCategory:list', 'movementCategory:detail',
    ])]
    #[SerializedName('details')]
    private string $description;

    #[ORM\OneToMany(targetEntity: ContentMovementCategory::class, mappedBy: "movementCategory", cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    public function __construct()
    {
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentMovementCategory $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setMovementCategory($this);
        }
        return $this;
    }

    public function removeContent(ContentMovementCategory $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentMovementCategory
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
