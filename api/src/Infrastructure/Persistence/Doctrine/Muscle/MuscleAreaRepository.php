<?php

namespace App\Infrastructure\Persistence\Doctrine\Muscle;

use App\Domain\Muscle\Entity\MuscleArea;
use App\Domain\Muscle\Repository\MuscleAreaRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class MuscleAreaRepository extends ServiceEntityRepository implements MuscleAreaRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, MuscleArea::class);
    }

    public function findOneById(string $id): ?MuscleArea
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?MuscleArea
    {
        return $this->find($slug);
    }

    public function findByFilters(
        ?string $sortBy,
        ?string $sortOrder,
        int     $offset = 0,
        int     $limit = 10,
    ): array {
        return [];
    }

    public function save(MuscleArea $muscleArea): void
    {
        $this->entityManager->persist($muscleArea);
        $this->entityManager->flush();
    }

    public function delete(MuscleArea $muscleArea): void
    {
        $this->entityManager->remove($muscleArea);
        $this->entityManager->flush();
    }
}
