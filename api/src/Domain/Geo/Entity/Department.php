<?php

namespace App\Domain\Geo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'department')]
class Department
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "Department ID")]
    #[Groups([
        'PUBLIC',
        'ADMIN'
    ])]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups([
        'ADMIN'
    ])]
    private string $code;

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

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(nullable: false)]
    private Region $region;

    #[ORM\OneToMany(targetEntity: City::class, mappedBy: 'department')]
    private Collection $cities;

    public function __construct(
        string $code,
        string $slug,
        string $name,
        Region $region,
    ) {
        $this->id = Uuid::v7();

        $this->code   = $code;
        $this->slug   = $slug;
        $this->name   = $name;
        $this->region = $region;

        $this->cities = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
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

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function setRegion(Region $region): self
    {
        $this->region = $region;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CITIES
    // -----------------------------------------------------------------------------------------------------------------
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities->add($this);
        }
        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->removeElement($city)) {
            $this->cities->removeElement($city);
        }
        return $this;
    }
}
