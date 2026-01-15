<?php

namespace App\Domain\Security\Entity;

use App\Infrastructure\Doctrine\Repository\Security\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\Table(name: 'permission')]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "Permission ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    #[OA\Property(description: "Permission code", example: "EQUIPMENT_LIST")]
    private string $code;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "Permission label", example: "List equipments")]
    private string $label;

    #[ORM\Column(type: 'string', nullable: true)]
    #[OA\Property(description: "Permission resource", example: "equipment")]
    private ?string $resource = null;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    public function __construct(
        string $code,
        string $label,
    ) {

        $this->code  = $code;
        $this->label = $label;
        $this->roles = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = strtoupper($code);
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getResource(): ?string
    {
        return $this->resource;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function setResource(?string $resource): self
    {
        $this->resource = $resource;
        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }
}
