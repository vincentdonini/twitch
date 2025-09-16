<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodVersionVariantMovement;
use App\Domain\Wod\Repository\WodVersionVariantMovementRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WodVersionVariantMovementRepository extends ServiceEntityRepository implements WodVersionVariantMovementRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, WodVersionVariantMovement::class);
    }

    public function findOneById(string $id): ?WodVersionVariantMovement
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?WodVersionVariantMovement
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /** @return WodVersionVariantMovement[] */
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

    public function save(WodVersionVariantMovement $wodVersionVariantMovement): void
    {
        $this->entityManager->persist($wodVersionVariantMovement);
        $this->entityManager->flush();
    }

    public function delete(WodVersionVariantMovement $wodVersionVariantMovement): void
    {
        $this->entityManager->remove($wodVersionMovement);
        $this->entityManager->flush();
    }
}
