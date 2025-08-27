<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Repository\WodTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WodTypeRepository extends ServiceEntityRepository implements WodTypeRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, WodType::class);
    }

    public function findOneById(string $id): ?WodType
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?WodType
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    public function findByFilters(
        ?string $sortBy,
        ?string $sortOrder,
        int     $offset = 0,
        int     $limit = 10,
    ): array {
        return [];
    }

    public function save(WodType $wodType): void
    {
        $this->entityManager->persist($wodType);
        $this->entityManager->flush();
    }

    public function delete(WodType $wodType): void
    {
        $this->entityManager->remove($wodType);
        $this->entityManager->flush();
    }
}
