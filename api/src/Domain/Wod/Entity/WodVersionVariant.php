<?php

namespace App\Domain\Wod\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Wod\Enum\Gender;
use App\Infrastructure\ApiPlatform\Wod\Provider\WodVersionVariantProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodVersionVariantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WodVersionVariantRepository::class)]
#[ORM\Table(name: 'wod_version_variants')]
#[ApiResource(
    formats                      : ['json', 'jsonapi'],
    paginationClientEnabled      : false,
    paginationClientItemsPerPage : true,
    paginationEnabled            : true,
    paginationItemsPerPage       : 10,
    paginationMaximumItemsPerPage: 50,
)]
#[GetCollection(
    normalizationContext         : [
        'groups' => [
            'wodVersionVariant:list',
        ],
    ],
    provider            : WodVersionVariantProvider::class,
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'wodVersionVariant:detail',
        ],
    ],
    provider            : WodVersionVariantProvider::class,
)]
class WodVersionVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariant:list', 'wodVersionVariant:detail',
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: WodVersion::class, fetch: 'EAGER', inversedBy: 'wodVersionVariants')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'wodVersionVariant:list', 'wodVersionVariant:detail'
    ])]
    private ?WodVersion $wodVersion = null;

    #[ORM\Column(type: 'string', length: 10, enumType: Gender::class)]
    #[Assert\Choice(callback: [Gender::class, 'cases'])]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariant:list', 'wodVersionVariant:detail',
    ])]
    private ?Gender $gender = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariant:list', 'wodVersionVariant:detail',
    ])]
    private ?int $rounds = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariant:list', 'wodVersionVariant:detail',
    ])]
    private ?int $timeCap = null;

    #[ORM\Column(type: Types::JSON)]
    private array $descriptions = [];

    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariant:list', 'wodVersionVariant:detail',
    ])]
    #[SerializedName('description')]
    private string $description;

    #[ORM\OneToMany(targetEntity: WodVersionVariantMovement::class, mappedBy: 'wodVersionVariant', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    #[Groups([
        'wod:list', 'wod:detail',
        'wodVersionVariant:list', 'wodVersionVariant:detail',
    ])]
    private Collection $wodVersionVariantMovements;

    public function __construct()
    {
        $this->wodVersionVariantMovements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWodVersion(): ?WodVersion
    {
        return $this->wodVersion;
    }

    public function setWodVersion(?WodVersion $wodVersion): self
    {
        $this->wodVersion = $wodVersion;
        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(?Gender $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getRounds(): ?int
    {
        return $this->rounds;
    }

    public function setRounds(?int $rounds): self
    {
        $this->rounds = $rounds;
        return $this;
    }

    public function getTimeCap(): ?int
    {
        return $this->timeCap;
    }

    public function setTimeCap(?int $timeCap): self
    {
        $this->timeCap = $timeCap;
        return $this;
    }

    public function getDescriptions(): array
    {
        return $this->descriptions;
    }

    public function setDescriptions(array $descriptions): self
    {
        $this->descriptions = $descriptions;
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

    /**
     * @return Collection<int, WodVersionVariantMovement>
     */
    public function getWodVersionVariantMovements(): Collection
    {
        return $this->wodVersionVariantMovements;
    }

    public function addWodVersionVariantMovement(WodVersionVariantMovement $movement): self
    {
        if (!$this->wodVersionVariantMovements->contains($movement)) {
            $this->wodVersionVariantMovements[] = $movement;
            $movement->setWodVersionVariant($this);
        }
        return $this;
    }

    public function removeWodVersionVariantMovement(WodVersionVariantMovement $movement): self
    {
        if ($this->wodVersionVariantMovements->removeElement($movement)) {
            if ($movement->getWodVersionVariant() === $this) {
                $movement->setWodVersionVariant(null);
            }
        }
        return $this;
    }
}
