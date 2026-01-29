<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Enum\GenderEnum;
use App\Domain\Wod\Ports\WodVariantDALInterface;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class WodVariantRepository extends AbstractEntityRepository implements WodVariantDALInterface
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
        return WodVariant::class;
    }

    public function getManager(): string
    {
        return WodVariant::class;
    }

    public function getById(int $id): ?WodVariant
    {
        return $this->createQueryBuilder('wv')
            ->where('wv.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByWodIdAndWodDivisionId(int $wodId, int $wodDivisionId): ?WodVariant
    {
        return $this->createQueryBuilder('wv')
            ->where('wv.wod = :wodId')
            ->setParameter('wodId', $wodId)
            ->andWhere('wv.wodDivision = :wodDivisionId')
            ->setParameter('wodDivisionId', $wodDivisionId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByCriteria(
        Wod          $wod,
        WodDivision  $division,
        ?WodAgeRange $ageRange,
        ?GenderEnum $gender
    ): ?WodVariant {
        $qb = $this->createQueryBuilder('wv')
            ->where('v.wod = :wod')
            ->andWhere('v.wodDivision = :division')
            ->setParameter('wod', $wod->getId())
            ->setParameter('division', $division->getId());

        if ($ageRange !== null) {
            $qb->andWhere('v.wodAgeRange = :ageRange')
                ->setParameter('ageRange', $ageRange->getId());
        } else {
            $qb->andWhere('v.wodAgeRange IS NULL');
        }

        if ($gender !== null) {
            $qb->andWhere('v.gender = :gender')
                ->setParameter('gender', $gender->value);
        } else {
            $qb->andWhere('v.gender IS NULL');
        }

        return $qb->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
