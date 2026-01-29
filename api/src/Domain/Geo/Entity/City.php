<?php

namespace App\Domain\Geo\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'city')]
class City
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "City ID")]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 150)]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private string $slug;

    #[ORM\Column(type: 'string', length: 150)]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private string $name;

    #[ORM\Column(type: 'json')]
    #[Groups([
        'ADMIN'
    ])]
    private array $postalCodes = [];

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups([
        'ADMIN'
    ])]
    private string $inseeCode;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'ADMIN'
    ])]
    private int $population;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'ADMIN'
    ])]
    private int $area;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private string $coordinates;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'cities')]
    private Department $department;

    public function __construct(
        string     $slug,
        string     $name,
        array      $postalCodes,
        string     $coordinates,
        Department $department,
    ) {
        $this->id = Uuid::v7();

        $this->slug        = $slug;
        $this->name        = $name;
        $this->postalCodes = $postalCodes;
        $this->coordinates = $coordinates;
        $this->department  = $department;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPostalCodes(): array
    {
        return $this->postalCodes;
    }

    public function setPostalCodes(array $postalCodes): self
    {
        $this->postalCodes = $postalCodes;
        return $this;
    }

    public function hasPostalCode(string $postalCode): bool
    {
        return in_array($postalCode, $this->getPostalCodes(), true);
    }

    public function getInseeCode(): ?string
    {
        return $this->inseeCode;
    }

    public function setInseeCode(?string $inseeCode): self
    {
        $this->inseeCode = $inseeCode;
        return $this;
    }

    public function getPopulation(): string
    {
        return $this->population;
    }

    public function setPopulation(int $population): self
    {
        $this->population = $population;
        return $this;
    }

    public function getArea(): string
    {
        return $this->area;
    }

    public function setArea(int $area): self
    {
        $this->area = $area;
        return $this;
    }

    public function getCoordinates(): string
    {
        return $this->coordinates;
    }

    public function setCoordinates(string $coordinates): self
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): self
    {
        $this->department = $department;
        return $this;
    }
}
