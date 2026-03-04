<?php

namespace App\Domain\User\Entity;

use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Entity\Place;
use App\Domain\Security\Entity\Role;
use App\Infrastructure\Doctrine\Repository\User\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property(description: "User ID")]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(description: "User email", example: "user@example.com")]
    private string $email;

    #[ORM\Column]
    #[OA\Property(description: "User password.")]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[OA\Property(description: "User firstname", example: "John")]
    private string $firstName;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[OA\Property(description: "User lastname", example: "DOE")]
    private string $lastName;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    #[OA\Property(description: "User birth date", example: "1995-06-15")]
    private ?DateTimeImmutable $birthDate = null;

    #[ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'owners')]
    #[ORM\JoinTable(name: 'user_has_company')]
    private Collection $companies;

    #[ORM\ManyToMany(targetEntity: Place::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'user_has_place')]
    private Collection $places;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'user_role')]
    private Collection $roles;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName
    ) {
        $this->id = Uuid::v7();

        $this->email     = $email;
        $this->firstName = $firstName;
        $this->lastName  = $lastName;

        $this->roles     = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->places    = new ArrayCollection();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // GETTERS / SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function getId(): ?Uuid
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

    public function getBirthDate(): ?DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
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
        // Méthode requise par UserInterface
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Roles
    // -----------------------------------------------------------------------------------------------------------------

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

    // -----------------------------------------------------------------------------------------------------------------
    // Permissions
    // -----------------------------------------------------------------------------------------------------------------

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

    // -----------------------------------------------------------------------------------------------------------------
    // Companies
    // -----------------------------------------------------------------------------------------------------------------

    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->addOwner($this);
        }
        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->removeElement($company)) {
            $company->removeOwner($this);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Places
    // -----------------------------------------------------------------------------------------------------------------

    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->addUser($this);
        }
        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            $place->removeUser($this);
        }
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Business
    // -----------------------------------------------------------------------------------------------------------------

    public function getAge(): ?int
    {
        return $this->birthDate?->diff(new DateTimeImmutable())->y;
    }
}
