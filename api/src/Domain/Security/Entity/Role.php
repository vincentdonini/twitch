<?php

namespace App\Domain\Security\Entity;

use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Repository\Security\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: 'role')]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[OA\Property(description: "Role ID")]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    #[OA\Property(description: "Role code", example: "ROLE_ADMIN")]
    private string $code;

    #[ORM\Column(type: 'string')]
    #[OA\Property(description: "Role label", example: "Administrator")]
    private string $label;

    #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
    #[ORM\JoinTable(name: 'role_permission')]
    private Collection $permissions;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles')]
    private Collection $users;

    public function __construct(
        string $code,
        string $label,
    ) {
        $this->code        = $code;
        $this->label       = $label;
        $this->permissions = new ArrayCollection();
        $this->users       = new ArrayCollection();
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

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }
        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);
        return $this;
    }

    public function hasPermission(string $permissionCode): bool
    {
        foreach ($this->permissions as $permission) {
            if ($permission->getCode() === $permissionCode) {
                return true;
            }
        }
        return false;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }
}
