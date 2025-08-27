<?php

namespace App\Infrastructure\Persistence\Doctrine\Muscle;

use App\Domain\Muscle\Entity\MuscleGroup;
use App\Domain\Muscle\Repository\MuscleGroupRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class MuscleGroupRepository extends ServiceEntityRepository implements MuscleGroupRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, MuscleGroup::class);
    }

    public function findOneById(string $id): ?MuscleGroup
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?MuscleGroup
    {
        return $this->find($slug);
    }

    /** @return MuscleGroup[] */
    public function findAllByAreaId(string $areaId): array
    {
        return $this->findBy(['area' => $areaId]);
    }

    public function findOneByAreaId(string $groupId, string $areaId): ?MuscleGroup
    {
        return $this->findOneBy(['id' => $groupId, 'area' => $areaId]);
    }

    public function findByFilters(
        ?string $sortBy,
        ?string $sortOrder,
        int     $offset = 0,
        int     $limit = 10,
    ): array {
        return [];
    }

    public function save(MuscleGroup $muscleGroup): void
    {
        $this->entityManager->persist($muscleGroup);
        $this->entityManager->flush();
    }

    public function delete(MuscleGroup $muscleGroup): void
    {
        $this->entityManager->remove($muscleGroup);
        $this->entityManager->flush();
    }
}
