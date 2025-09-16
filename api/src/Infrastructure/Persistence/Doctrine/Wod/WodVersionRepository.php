<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Repository\WodVersionRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class WodVersionRepository extends ServiceEntityRepository implements WodVersionRepositoryInterface
{
    use FilterableTrait;

    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, WodVersion::class);
    }

    public function findOneById(string $id): ?WodVersion
    {
        return $this->find($id);
    }

    public function findOneByName(string $name): ?WodVersion
    {
        return $this->find($name);
    }

    public function save(WodVersion $wodVersion): void
    {
        $this->entityManager->persist($wodVersion);
        $this->entityManager->flush();
    }

    public function delete(WodVersion $wodVersion): void
    {
        $this->entityManager->remove($wodVersion);
        $this->entityManager->flush();
    }

//    protected function applyCustomFilters(QueryBuilder $qb, string $alias, array $filters): void
//    {
//        $qb
//            ->leftJoin("$alias.wodVersionMovements", "wve")
//            ->leftJoin("wve.movement", "e")
//            ->leftJoin("e.equipment", "eq");
//
//        // Filter by Wod
//        if (!empty($filters['wodId'])) {
//            $qb->andWhere($qb->expr()->in("$alias.wod", ':wodId'))
//                ->setParameter('wodId', $filters['wodId']);
//        }
//
//        // Filter by WodVersion type
//        if (!empty($filters['typeIds'])) {
//            $qb->andWhere($qb->expr()->in("$alias.wodVersionType", ':typeIds'))
//                ->setParameter('typeIds', $filters['typeIds']);
//        }
//
//        // Filter by max duration
//        if (!empty($filters['durationMax'])) {
//            $qb->andWhere("$alias.duration <= :durationMax")
//                ->setParameter('durationMax', $filters['durationMax']);
//        }
//
//        // Filter by equipments
//        if (!empty($filters['allowedEquipmentIds']) || !empty($filters['disallowedEquipmentIds'])) {
//            $this->applyEquipmentFilters($qb, $alias, $filters);
//        }
//
//        // Filter by movements
//        if (!empty($filters['allowedMovementIds']) || !empty($filters['disallowedMovementIds'])) {
//            $this->applyMovementFilters($qb, $alias, $filters);
//        }
//    }
//
//    private function applyEquipmentFilters(QueryBuilder $qb, string $alias,array $filters): void
//    {
//        $subQb = $qb->getEntityManager()->createQueryBuilder();
//        $subQb->select('wv_sub.id')
//            ->from(WodVersion::class, 'wv_sub')
//            ->leftJoin('wv_sub.wodVersionMovements', 'wve_sub')
//            ->leftJoin('wve_sub.movement', 'e_sub')
//            ->leftJoin('e_sub.equipment', 'eq_sub')
//            ->groupBy('wv_sub.id');
//
//        $havingExprsEquipments = [];
//
//        if (!empty($filters['allowedEquipmentIds'])) {
//            $havingExprsEquipments[] = $subQb->expr()->eq(
//                'SUM(CASE WHEN eq_sub.id IN (:allowedEquipmentIds) THEN 1 ELSE 0 END)',
//                count($filters['allowedEquipmentIds'])
//            );
//        }
//
//        if (!empty($filters['disallowedEquipmentIds'])) {
//            $havingExprsEquipments[] = $subQb->expr()->eq(
//                'SUM(CASE WHEN eq_sub.id IN (:disallowedEquipmentIds) THEN 1 ELSE 0 END)',
//                0
//            );
//        }
//
//        if ($havingExprsEquipments) {
//            $subQb->having(call_user_func_array([$subQb->expr(), 'andX'], $havingExprsEquipments));
//        }
//
//        $qb->andWhere($qb->expr()->in("$alias.id", $subQb->getDQL()));
//
//        // Set parameters for equipment filters
//        if (!empty($filters['allowedEquipmentIds'])) {
//            $qb->setParameter('allowedEquipmentIds', $filters['allowedEquipmentIds']);
//        }
//
//        if (!empty($filters['disallowedEquipmentIds'])) {
//            $qb->setParameter('disallowedEquipmentIds', $filters['disallowedEquipmentIds']);
//        }
//    }
//
//    private function applyMovementFilters(QueryBuilder $qb, string $alias, array $filters): void
//    {
//        $subQbMovements = $qb->getEntityManager()->createQueryBuilder();
//        $subQbMovements->select('wv_sub2.id')
//            ->from(WodVersion::class, 'wv_sub2')
//            ->leftJoin('wv_sub2.wodVersionMovements', 'wve_sub2')
//            ->leftJoin('wve_sub2.movement', 'e_sub2')
//            ->groupBy('wv_sub2.id');
//
//        $havingExprsMovements = [];
//
//        if (!empty($filters['allowedMovementIds'])) {
//            $havingExprsMovements[] = $subQbMovements->expr()->eq(
//                'SUM(CASE WHEN e_sub2.id IN (:allowedMovementIds) THEN 1 ELSE 0 END)',
//                count($filters['allowedMovementIds'])
//            );
//        }
//
//        if (!empty($filters['disallowedMovementIds'])) {
//            $havingExprsMovements[] = $subQbMovements->expr()->eq(
//                'SUM(CASE WHEN e_sub2.id IN (:disallowedMovementIds) THEN 1 ELSE 0 END)',
//                0
//            );
//        }
//
//        if ($havingExprsMovements) {
//            $subQbMovements->having(call_user_func_array([$subQbMovements->expr(), 'andX'], $havingExprsMovements));
//        }
//
//        $qb->andWhere($qb->expr()->in("$alias.id", $subQbMovements->getDQL()));
//
//        // Set parameters for movement filters
//        if (!empty($filters['allowedMovementIds'])) {
//            $qb->setParameter('allowedMovementIds', $filters['allowedMovementIds']);
//        }
//
//        if (!empty($filters['disallowedMovementIds'])) {
//            $qb->setParameter('disallowedMovementIds', $filters['disallowedMovementIds']);
//        }
//    }

    protected function getAlias(): string
    {
        return 'wv';
    }

    protected function getSearchableFields(): array
    {
        return [
            'notes' => 'like',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'id',
            'notes',
            'duration',
        ];
    }
}
