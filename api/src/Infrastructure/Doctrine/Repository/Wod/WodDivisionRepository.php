<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class WodDivisionRepository extends AbstractEntityRepository implements WodDivisionDALInterface
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
        return WodDivision::class;
    }

    public function getManager(): string
    {
        return WodDivision::class;
    }

    public function getById(Uuid $id): ?WodDivision
    {
        return $this->find($id);
    }

    public function getBySLug(string $slug): ?WodDivision
    {
        return $this->createQueryBuilder('wd')
            ->where('wd.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listWodDivisions(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('wd');

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(wd.id) as count')
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
                SELECT d.id as id, COUNT(DISTINCT w.id) as cnt
                FROM ' . WodVariant::class . ' wv
                JOIN wv.wodDivision d
                JOIN wv.wod w
                GROUP BY d.id
            ')
            ->getResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string)$row['id']] = (int)$row['cnt'];
        }
        return $counts;
    }
}
