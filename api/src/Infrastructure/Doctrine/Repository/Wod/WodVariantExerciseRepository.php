<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\WodVariantExercise;
use App\Domain\Wod\Ports\WodVariantExerciseDALInterface;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class WodVariantExerciseRepository extends AbstractEntityRepository implements WodVariantExerciseDALInterface
{
    use LocaleTrait;

    public function __construct(
        protected ManagerRegistry     $registry,
        private readonly RequestStack $requestStack,
        private readonly string       $locale,
    ) {
        parent::__construct($registry);
    }

    public function getClass(): string
    {
        return WodVariantExercise::class;
    }

    public function getManager(): string
    {
        return WodVariantExercise::class;
    }

    public function getById(string $id): ?WodVariantExercise
    {
        return $this->createQueryBuilder('wvve')
            ->where('wvve.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
