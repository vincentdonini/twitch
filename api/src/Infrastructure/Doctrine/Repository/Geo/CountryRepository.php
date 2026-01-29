<?php

namespace App\Infrastructure\Doctrine\Repository\Geo;

use App\Domain\Geo\Entity\Country;
use App\Domain\Geo\Ports\CountryDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Doctrine\Sorts\DoctrineSortApplier;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class CountryRepository extends AbstractEntityRepository implements CountryDALInterface
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
        return Country::class;
    }

    public function getManager(): string
    {
        return Country::class;
    }

    public function getById(string $id): ?Country
    {
        return $this->find($id);
    }

    public function listCountries(
        int              $page = 1,
        int              $limit = 10,
        FilterCollection $filters = null,
        SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('c');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())
                ->apply($qb, 'c', $filters);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            $qb = (new DoctrineSortApplier())
                ->apply($qb, 'c', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(c.id) as count')
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
