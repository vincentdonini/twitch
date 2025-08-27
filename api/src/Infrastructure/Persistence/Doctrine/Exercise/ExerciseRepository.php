<?php

namespace App\Infrastructure\Persistence\Doctrine\Exercise;

use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Repository\ExerciseRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ExerciseRepository extends ServiceEntityRepository implements ExerciseRepositoryInterface
{
    use FilterableTrait;

    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, Exercise::class);
    }

    public function findOneById(string $id): ?Exercise
    {
        return $this->find($id);
    }

    public function findOneByName(string $name): ?Exercise
    {
        return $this->find($name);
    }

    public function save(Exercise $exercise): void
    {
        $this->entityManager->persist($exercise);
        $this->entityManager->flush();
    }

    public function delete(Exercise $exercise): void
    {
        $this->entityManager->remove($exercise);
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
