<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Ports\WodDivisionDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function getById(string $id): ?WodDivision
    {
        return $this->createQueryBuilder('wd')
            ->where('wd.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
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
        int $page = 1,
        int $limit = 10,
            $filters = []
    ): LightPaginator {
        $locale = $this->getLocale();

        $qb = $this->createQueryBuilder('wd');

        $hasJoinedContent = false;

        if (!empty($filters['filters'])) {

            // WodDivision
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($filters['filters']['slug'])) {
                $qb
                    ->andWhere('wd.slug LIKE :slug')
                    ->setParameter(
                        'slug',
                        sprintf(
                            '%%%s%%',
                            $filters['filters']['slug']
                        )
                    );
            }

            // ContentWodDivision
            // ---------------------------------------------------------------------------------------------------------
            $contentFilters = ['name', 'summary', 'details'];

            foreach ($contentFilters as $field) {
                if (!empty($filters['filters'][$field])) {
                    if (!$hasJoinedContent) {
                        $qb->join('wd.contents', 'wdc');
                        $qb->andWhere('wdc.locale = :locale');
                        $qb->setParameter('locale', $locale);
                        $hasJoinedContent = true;
                    }

                    $qb->andWhere(sprintf('wdc.%s LIKE :%s', $field, $field))
                        ->setParameter(
                            $field,
                            sprintf(
                                '%%%s%%',
                                $filters['filters'][$field]
                            )
                        );
                }
            }
        }

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
}
