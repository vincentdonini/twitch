<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
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
}
