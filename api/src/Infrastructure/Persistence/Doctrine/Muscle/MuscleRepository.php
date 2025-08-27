<?php

namespace App\Infrastructure\Persistence\Doctrine\Muscle;

use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Repository\MuscleRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class MuscleRepository extends ServiceEntityRepository implements MuscleRepositoryInterface
{
    use FilterableTrait;

    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, Muscle::class);
    }

    public function findOneById(string $id): ?Muscle
    {
        return $this->find($id);
    }

    public function findOneBySlug(string $slug): ?Muscle
    {
        return $this->find($slug);
    }

    /** @return Muscle[] */
    public function findAllByGroupId(string $groupId): array
    {
        return $this->findBy(['group' => $groupId]);
    }

    /** @return Muscle[] */
    public function findAllByAreaIdAndGroupId(string $areaId, string $groupId): array
    {
        return $this->findBy(['area' => $areaId, 'group' => $groupId]);
    }

    public function save(Muscle $muscle): void
    {
        $this->entityManager->persist($muscle);
        $this->entityManager->flush();
    }

    public function delete(Muscle $muscle): void
    {
        $this->entityManager->remove($muscle);
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
