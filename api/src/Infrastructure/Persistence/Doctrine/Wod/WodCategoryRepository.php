<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Repository\WodCategoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WodCategoryRepository extends ServiceEntityRepository implements WodCategoryRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, WodCategory::class);
    }

    public function findOneById(string $id): ?WodCategory
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?WodCategory
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

    public function save(WodCategory $wodCategory): void
    {
        $this->entityManager->persist($wodCategory);
        $this->entityManager->flush();
    }

    public function delete(WodCategory $wodCategory): void
    {
        $this->entityManager->remove($wodCategory);
        $this->entityManager->flush();
    }
}
