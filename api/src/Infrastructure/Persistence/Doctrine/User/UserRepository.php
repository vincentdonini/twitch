<?php

namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    use FilterableTrait;

    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, User::class);
    }

    public function findAllUsers(): array
    {
        return $this->findAll();
    }

    public function findUserById(int $id): ?User
    {
        return $this->find($id);
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    protected function getAlias(): string
    {
        return 'u';
    }

    protected function getSearchableFields(): array
    {
        return [
            'email'     => 'like',
            'firstName' => 'like',
            'lastName'  => 'like',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'id',
            'email',
            'firstName',
            'lastName',
        ];
    }
}
