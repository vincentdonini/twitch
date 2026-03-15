<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class WodCategoryRepository extends AbstractEntityRepository implements WodCategoryDALInterface
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
        return WodCategory::class;
    }

    public function getManager(): string
    {
        return WodCategory::class;
    }

    public function getById(Uuid $id): ?WodCategory
    {
        return $this->find($id);
    }

    public function listWodCategories(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('wc');

        // Pagination
        // -------------------------------------------------------------------------------------------------------------

        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(wc.id) as count')
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

    public function getWodCounts(): array
    {
        $rows = $this->getEntityManager()
            ->createQuery('
                SELECT c.id as id, COUNT(w.id) as cnt
                FROM ' . Wod::class . ' w
                JOIN w.wodCategory c
                GROUP BY c.id
            ')
            ->getResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string)$row['id']] = (int)$row['cnt'];
        }
        return $counts;
    }
}
