<?php

namespace App\Domain\Equipment\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Content\Entity\ContentEquipment;
use App\Infrastructure\ApiPlatform\Equipment\Provider\EquipmentProvider;
use App\Infrastructure\Persistence\Doctrine\Equipment\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\Table(name: 'equipments')]
#[ApiResource(
    formats                      : ['json', 'jsonapi'],
    paginationClientEnabled      : true,
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
    private ?string $name = null;

    #[Groups([
        'wod:list', 'wod:detail',
        'equipment:list', 'equipment:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('summary')]
    private string $summary;

    #[Groups([
        'wod:list', 'wod:detail',
        'equipment:list', 'equipment:detail',
        'movement:list', 'movement:detail',
    ])]
    #[SerializedName('details')]
    private string $details;

    #[ORM\OneToMany(targetEntity: ContentEquipment::class, mappedBy: "equipment", cascade: ["persist", "remove"], orphanRemoval: true)]
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

    // CONTENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(ContentEquipment $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
            $content->setEquipment($this);
        }
        return $this;
    }

    public function removeContent(ContentEquipment $content): self
    {
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
        }
        return $this;
    }

    public function getContentByLocale(string $locale): ?ContentEquipment
    {
        foreach ($this->contents as $content) {
            if ($content->getLocale() === $locale) {
                return $content;
            }
        }
        return null;
    }
}
