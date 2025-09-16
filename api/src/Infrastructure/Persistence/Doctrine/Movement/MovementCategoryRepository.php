<?php

namespace App\Infrastructure\Persistence\Doctrine\Movement;

use App\Domain\Movement\Entity\MovementCategory;
use App\Domain\Movement\Repository\MovementCategoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class MovementCategoryRepository extends ServiceEntityRepository implements MovementCategoryRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, MovementCategory::class);
    }

    public function findOneById(string $id): ?MovementCategory
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?MovementCategory
    {
        return $this->find($slug);
    }

    public function findByFilters(
        ?string $sortBy,
        ?string $sortOrder,
        int     $offset = 0,
        int     $limit = 10,
        ?array  $typesArray = null,
    ): array {
        return [];
    }

    public function save(MovementCategory $movementCategory): void
    {
        $this->entityManager->persist($movementCategory);
        $this->entityManager->flush();
    }

    public function delete(MovementCategory $movementCategory): void
    {
        $this->entityManager->remove($movementCategory);
        $this->entityManager->flush();
    }
}
