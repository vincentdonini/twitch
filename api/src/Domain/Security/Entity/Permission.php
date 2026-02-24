<?php

namespace App\Domain\Security\Entity;

use App\Infrastructure\Doctrine\Repository\Security\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[ORM\Table(name: 'permission')]
class Permission
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $label;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $resource = null;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    public function __construct(
        string $code,
        string $label,
    ) {
        $this->id = Uuid::v7();

        $this->code  = $code;
        $this->label = $label;

        $this->roles = new ArrayCollection();
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

    // -----------------------------------------------------------------------------------------------------------------
    // ROLES
    // -----------------------------------------------------------------------------------------------------------------
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
            $role->addPermission($this);
        }
        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }
        return $this;
    }
}
