<?php

namespace App\Infrastructure\Persistence\Doctrine\Movement;

use App\Domain\Movement\Entity\Movement;
use App\Domain\Movement\Repository\MovementRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class MovementRepository extends ServiceEntityRepository implements MovementRepositoryInterface
{
    use FilterableTrait;
    
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, Movement::class);
    }

    public function findOneById(string $id): ?Movement
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?Movement
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

    public function save(Movement $movement): void
    {
        $this->entityManager->persist($movement);
        $this->entityManager->flush();
    }

    public function delete(Movement $movement): void
    {
        $this->entityManager->remove($movement);
        $this->entityManager->flush();
    }


    protected function getAlias(): string
    {
        return 'm';
    }

    protected function getSearchableFields(): array
    {
        return ['slug' => 'like'];
    }

    protected function getSortableFields(): array
    {
        return ['id', 'slug'];
    }
}
