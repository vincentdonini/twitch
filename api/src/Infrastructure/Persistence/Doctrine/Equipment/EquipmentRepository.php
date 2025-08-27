<?php

namespace App\Infrastructure\Persistence\Doctrine\Equipment;

use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Equipment\Repository\EquipmentRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class EquipmentRepository extends ServiceEntityRepository implements EquipmentRepositoryInterface
{
    use FilterableTrait;

    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, Equipment::class);
    }

    public function findOneById(string $id): ?Equipment
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?Equipment
    {
        return $this->find($slug);
    }

    public function save(Equipment $equipment): void
    {
        $this->entityManager->persist($equipment);
        $this->entityManager->flush();
    }

    public function delete(Equipment $equipment): void
    {
        $this->entityManager->remove($equipment);
        $this->entityManager->flush();
    }

    protected function getAlias(): string
    {
        return 'e';
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
