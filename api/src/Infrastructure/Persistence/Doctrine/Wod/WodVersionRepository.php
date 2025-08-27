<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Repository\WodVersionRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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
//            ->leftJoin("$alias.wodVersionExercises", "wve")
//            ->leftJoin("wve.exercise", "e")
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
//        // Filter by exercises
//        if (!empty($filters['allowedExerciseIds']) || !empty($filters['disallowedExerciseIds'])) {
//            $this->applyExerciseFilters($qb, $alias, $filters);
//        }
//    }
//
//    private function applyEquipmentFilters(QueryBuilder $qb, string $alias,array $filters): void
//    {
//        $subQb = $qb->getEntityManager()->createQueryBuilder();
//        $subQb->select('wv_sub.id')
//            ->from(WodVersion::class, 'wv_sub')
//            ->leftJoin('wv_sub.wodVersionExercises', 'wve_sub')
//            ->leftJoin('wve_sub.exercise', 'e_sub')
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
//    private function applyExerciseFilters(QueryBuilder $qb, string $alias, array $filters): void
//    {
//        $subQbExercises = $qb->getEntityManager()->createQueryBuilder();
//        $subQbExercises->select('wv_sub2.id')
//            ->from(WodVersion::class, 'wv_sub2')
//            ->leftJoin('wv_sub2.wodVersionExercises', 'wve_sub2')
//            ->leftJoin('wve_sub2.exercise', 'e_sub2')
//            ->groupBy('wv_sub2.id');
//
//        $havingExprsExercises = [];
//
//        if (!empty($filters['allowedExerciseIds'])) {
//            $havingExprsExercises[] = $subQbExercises->expr()->eq(
//                'SUM(CASE WHEN e_sub2.id IN (:allowedExerciseIds) THEN 1 ELSE 0 END)',
//                count($filters['allowedExerciseIds'])
//            );
//        }
//
//        if (!empty($filters['disallowedExerciseIds'])) {
//            $havingExprsExercises[] = $subQbExercises->expr()->eq(
//                'SUM(CASE WHEN e_sub2.id IN (:disallowedExerciseIds) THEN 1 ELSE 0 END)',
//                0
//            );
//        }
//
//        if ($havingExprsExercises) {
//            $subQbExercises->having(call_user_func_array([$subQbExercises->expr(), 'andX'], $havingExprsExercises));
//        }
//
//        $qb->andWhere($qb->expr()->in("$alias.id", $subQbExercises->getDQL()));
//
//        // Set parameters for exercise filters
//        if (!empty($filters['allowedExerciseIds'])) {
//            $qb->setParameter('allowedExerciseIds', $filters['allowedExerciseIds']);
//        }
//
//        if (!empty($filters['disallowedExerciseIds'])) {
//            $qb->setParameter('disallowedExerciseIds', $filters['disallowedExerciseIds']);
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
