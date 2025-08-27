<?php

namespace App\Infrastructure\Persistence\Doctrine\Exercise;

use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Exercise\Repository\ExerciseCategoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ExerciseCategoryRepository extends ServiceEntityRepository implements ExerciseCategoryRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, ExerciseCategory::class);
    }

    public function findOneById(string $id): ?ExerciseCategory
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?ExerciseCategory
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

    public function save(ExerciseCategory $exerciseCategory): void
    {
        $this->entityManager->persist($exerciseCategory);
        $this->entityManager->flush();
    }

    public function delete(ExerciseCategory $exerciseCategory): void
    {
        $this->entityManager->remove($exerciseCategory);
        $this->entityManager->flush();
    }
}
