<?php

namespace App\Domain\Geo\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'region')]
class Region
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "Region ID")]
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

    #[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'regions')]
    #[ORM\JoinColumn(nullable: false)]
    private Country $country;

    #[ORM\OneToMany(targetEntity: Department::class, mappedBy: 'region')]
    private Collection $departments;

    public function __construct(
        string  $code,
        string  $slug,
        string  $name,
        Country $country,
    ) {
        $this->id = Uuid::v7();

        $this->code    = $code;
        $this->slug    = $slug;
        $this->name    = $name;
        $this->country = $country;

        $this->departments = new ArrayCollection();
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

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // DEPARTMENTS
    // -----------------------------------------------------------------------------------------------------------------
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($this);
        }
        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            $this->departments->removeElement($department);
        }
        return $this;
    }
}
