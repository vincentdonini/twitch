<?php

namespace App\Domain\User\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Infrastructure\Persistence\Doctrine\User\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ApiResource(
    formats                      : ['json', 'jsonapi'],
    paginationClientEnabled      : false,
    paginationClientItemsPerPage : true,
    paginationEnabled            : true,
    paginationItemsPerPage       : 10,
    paginationMaximumItemsPerPage: 50,
    security: "is_granted('ROLE_ADMIN')"
)]
#[GetCollection(
    normalizationContext: [
        'groups' => [
            'user:list',
        ],
    ],
    security: "is_granted('ROLE_ADMIN')"
)]
#[Get(
    normalizationContext: [
        'groups' => [
            'user:detail',
        ],
    ],
    security: "is_granted('ROLE_ADMIN') or object.getId() == user.getId()"
)]
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

    #[ORM\Column(type: 'json')]
    #[OA\Property(description: "User roles", example: ["ROLE_USER"])]
    #[Groups(['user:list', 'user:detail'])]
    private array $roles = [];

    public function __construct()
    {
    }

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

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getRoles(): array
    {
        $roles   = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
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
        // Méthode requise par UserInterface, mais non utilisée ici.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
