<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodVersionType;
use App\Domain\Wod\Repository\WodVersionTypeRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WodVersionTypeRepository extends ServiceEntityRepository implements WodVersionTypeRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, WodVersionType::class);
    }

    public function findOneById(string $id): ?WodVersionType
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?WodVersionType
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

    public function save(WodVersionType $wodVersionType): void
    {
        $this->entityManager->persist($wodVersionType);
        $this->entityManager->flush();
    }

    public function delete(WodVersionType $wodVersionType): void
    {
        $this->entityManager->remove($wodVersionType);
        $this->entityManager->flush();
    }
}
