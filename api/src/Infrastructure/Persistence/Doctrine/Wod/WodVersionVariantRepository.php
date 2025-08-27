<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodVersionVariant;
use App\Domain\Wod\Repository\WodVersionVariantRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WodVersionVariantRepository extends ServiceEntityRepository implements WodVersionVariantRepositoryInterface
{
    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, WodVersionVariant::class);
    }

    public function findOneById(string $id): ?WodVersionVariant
    {
        return $this->find($id);
    }

    public function findOneByName(string $name): ?WodVersionVariant
    {
        return $this->find($name);
    }

    public function save(WodVersionVariant $wodVersionVariant): void
    {
        $this->entityManager->persist($wodVersionVariant);
        $this->entityManager->flush();
    }

    public function delete(WodVersionVariant $wodVersionVariant): void
    {
        $this->entityManager->remove($wodVersionVariant);
        $this->entityManager->flush();
    }

    protected function getAlias(): string
    {
        return 'wvv';
    }

    protected function getSearchableFields(): array
    {
        return [
            'description' => 'like',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'id',
            'gender',
            'timeCap',
        ];
    }
}
