<?php

namespace App\Domain\User\Entity;

use App\Domain\Security\Entity\Role;
use App\Infrastructure\Doctrine\Repository\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: "User ID")]
    #[Groups(['user:list', 'user:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(description: "User email", example: "user@example.com")]
    #[Groups(['user:list', 'user:detail'])]
    private ?string $email = null;

    #[ORM\Column]
    #[OA\Property(description: "User password.", example: "Azerty123!")]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[OA\Property(description: "User firstname", example: "John")]
    #[Groups(['user:list', 'user:detail'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[OA\Property(description: "User lastname", example: "DOE")]
    #[Groups(['user:list', 'user:detail'])]
    private ?string $lastName = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
        return $this;
    }

    public function getRoles(): array
    {
        return array_map(fn(Role $role) => $role->getCode(), $this->roles->toArray());
    }

    public function hasRole(string $roleCode): bool
    {
        return in_array($roleCode, $this->getRoles(), true);
    }

    public function getRoleEntities(): Collection
    {
        return $this->roles;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);
        return $this;
    }

    public function getPermissions(): array
    {
        $permissions = [];

        foreach ($this->roles as $role) {
            foreach ($role->getPermissions() as $permission) {
                $permissions[] = $permission->getCode();
            }
        }

        return array_unique($permissions);
    }

    public function hasPermission(string $permissionCode): bool
    {
        return in_array($permissionCode, $this->getPermissions(), true);
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Méthode requise par UserInterface, mais non utilisée ici.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
