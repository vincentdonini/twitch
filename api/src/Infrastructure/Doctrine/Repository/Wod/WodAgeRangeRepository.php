<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Ports\WodAgeRangeDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class WodAgeRangeRepository extends AbstractEntityRepository implements WodAgeRangeDALInterface
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
        return WodAgeRange::class;
    }

    public function getManager(): string
    {
        return WodAgeRange::class;
    }

    public function getById(string $id): ?WodAgeRange
    {
        return $this->createQueryBuilder('war')
            ->where('war.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getBySlug(string $slug): ?WodAgeRange
    {
        return $this->createQueryBuilder('war')
            ->where('war.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listWodAgeRanges(
        int $page = 1,
        int $limit = 10,
            $filters = []
    ): LightPaginator {
        $locale = $this->getLocale();

        $qb = $this->createQueryBuilder('war');

        $hasJoinedContent = false;

        if (!empty($filters['filters'])) {

            // WodAgeRange
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($filters['filters']['slug'])) {
                $qb
                    ->andWhere('war.slug LIKE :slug')
                    ->setParameter(
                        'slug',
                        sprintf(
                            '%%%s%%',
                            $filters['filters']['slug']
                        )
                    );
            }

            // ContentWodAgeRange
            // ---------------------------------------------------------------------------------------------------------
//            $contentFilters = ['name', 'summary', 'details'];
//
//            foreach ($contentFilters as $field) {
//                if (!empty($filters['filters'][$field])) {
//                    if (!$hasJoinedContent) {
//                        $qb->join('war.contents', 'warc');
//                        $qb->andWhere('warc.locale = :locale');
//                        $qb->setParameter('locale', $locale);
//                        $hasJoinedContent = true;
//                    }
//
//                    $qb->andWhere(sprintf('warc.%s LIKE :%s', $field, $field))
//                        ->setParameter(
//                            $field,
//                            sprintf(
//                                '%%%s%%',
//                                $filters['filters'][$field]
//                            )
//                        );
//                }
//            }
        }

        $aggQb = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(war.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult();

        $count = (int) $aggResult['count'];

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
