<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodVersionVariantExercise;
use App\Domain\Wod\Repository\WodVersionVariantExerciseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WodVersionVariantExerciseRepository extends ServiceEntityRepository implements WodVersionVariantExerciseRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, WodVersionVariantExercise::class);
    }

    public function findOneById(string $id): ?WodVersionVariantExercise
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?WodVersionVariantExercise
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /** @return WodVersionVariantExercise[] */
    public function findAll(): array
    {
        return $this->findAll();
    }

    public function findByFilters(
        ?string $sortBy,
        ?string $sortOrder,
        int     $offset = 0,
        int     $limit = 10,
    ): array {
        return [];
    }

    public function save(WodVersionVariantExercise $wodVersionExercise): void
    {
        $this->entityManager->persist($wodVersionExercise);
        $this->entityManager->flush();
    }

    public function delete(WodVersionVariantExercise $wodVersionExercise): void
    {
        $this->entityManager->remove($wodVersionExercise);
        $this->entityManager->flush();
    }
}
