<?php

namespace App\Infrastructure\Doctrine\Repository\Equipment;

use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Equipment\Ports\EquipmentDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Doctrine\Sorts\DoctrineSortApplier;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class EquipmentRepository extends AbstractEntityRepository implements EquipmentDALInterface
{
    use LocaleTrait;

    public function __construct(
        private readonly string       $locale,
        protected ManagerRegistry     $registry,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($registry);
    }

    public function getClass(): string
    {
        return Equipment::class;
    }

    public function getManager(): string
    {
        return Equipment::class;
    }

    public function getById(Uuid $id): ?Equipment
    {
        return $this->find($id);
    }

    public function listEquipments(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('e');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            (new DoctrineFilterApplier())->apply($qb, 'e', $filters);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            (new DoctrineSortApplier())->apply($qb, 'e', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(DISTINCT e.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int)$aggResult['count'];

        $items = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return new LightPaginator(
            $items,
            $count,
            $page,
            $limit,
        );
    }
}
