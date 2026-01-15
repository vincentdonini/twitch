<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\WodType;
use App\Domain\Wod\Ports\WodTypeDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class WodTypeRepository extends AbstractEntityRepository implements WodTypeDALInterface
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
        return WodType::class;
    }

    public function getManager(): string
    {
        return WodType::class;
    }

    public function getById(string $id): ?WodType
    {
        return $this->createQueryBuilder('wt')
            ->where('wt.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listWodTypes(
        int $page = 1,
        int $limit = 10,
            $filters = []
    ): LightPaginator {
        $locale = $this->getLocale();

        $qb = $this->createQueryBuilder('wt');

        $hasJoinedContent = false;

        if (!empty($filters['filters'])) {

            // WodType
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($filters['filters']['slug'])) {
                $qb
                    ->andWhere('wt.slug LIKE :slug')
                    ->setParameter(
                        'slug',
                        sprintf(
                            '%%%s%%',
                            $filters['filters']['slug']
                        )
                    );
            }

            // ContentWodType
            // ---------------------------------------------------------------------------------------------------------
            $contentFilters = ['name', 'summary', 'details'];

            foreach ($contentFilters as $field) {
                if (!empty($filters['filters'][$field])) {
                    if (!$hasJoinedContent) {
                        $qb->join('wt.contents', 'wtc');
                        $qb->andWhere('wtc.locale = :locale');
                        $qb->setParameter('locale', $locale);
                        $hasJoinedContent = true;
                    }

                    $qb->andWhere(sprintf('wtc.%s LIKE :%s', $field, $field))
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

        $aggQb = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(wt.id) as count')
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
