<?php

namespace App\Infrastructure\Doctrine\Repository\Formula;

use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Ports\FormulaDALInterface;
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

class FormulaRepository extends AbstractEntityRepository implements FormulaDALInterface
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
        return Formula::class;
    }

    public function getManager(): string
    {
        return Formula::class;
    }

    public function getById(Uuid $id): ?Formula
    {
        return $this->find($id);
    }

    public function listFormulas(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('f');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())
                ->apply($qb, 'f', $filters);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            $qb = (new DoctrineSortApplier())
                ->apply($qb, 'f', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(f.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int)$aggResult['count'];

        if ($limit === -1) {
            $items = $qb
                ->getQuery()
                ->getResult();

            return new LightPaginator(
                $items,
                $count,
                1,
                $count
            );
        }

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

    public function listFormulasByPlaceId(
        Uuid              $placeId,
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('f')
            ->where('f.place = :placeId')
            ->setParameter('placeId', hex2bin(str_replace('-', '', (string)$placeId)));

        // Filters
        // -------------------------------------------------------------------------------------------------------------
//        if (!$filters->isEmpty()) {
//            $qb = (new DoctrineFilterApplier())
//                ->apply($qb, 'f', $filters);
//        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
//        if (!$sorts->isEmpty()) {
//            $qb = (new DoctrineSortApplier())
//                ->apply($qb, 'f', $sorts);
//        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(f.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int)$aggResult['count'];

        if ($limit === -1) {
            $items = $qb
                ->getQuery()
                ->getResult();

            return new LightPaginator(
                $items,
                $count,
                1,
                $count
            );
        }

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

    public function hasSubscriptions(Formula $formula): bool
    {
        return (bool) $this->createQueryBuilder('f')
            ->select('COUNT(s.id)')
            ->join('f.subscriptions', 's')
            ->where('f = :formula')
            ->setParameter('formula', hex2bin(str_replace('-', '', (string)$formula->getId())))
            ->getQuery()
            ->getSingleScalarResult();
    }
}
