<?php

namespace App\Infrastructure\Doctrine\Repository\Wod;

use App\Domain\Wod\Entity\WodCategory;
use App\Domain\Wod\Ports\WodCategoryDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function getById(string $id): ?WodCategory
    {
        return $this->createQueryBuilder('wc')
            ->where('wc.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listWodCategories(
        int $page = 1,
        int $limit = 10,
            $filters = []
    ): LightPaginator {
        $locale = $this->getLocale();

        $qb = $this->createQueryBuilder('wc');

        $hasJoinedContent = false;

        if (!empty($filters['filters'])) {

            // WodCategory
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($filters['filters']['slug'])) {
                $qb
                    ->andWhere('wc.slug LIKE :slug')
                    ->setParameter(
                        'slug',
                        sprintf(
                            '%%%s%%',
                            $filters['filters']['slug']
                        )
                    );
            }

            // ContentWodCategory
            // ---------------------------------------------------------------------------------------------------------
            $contentFilters = ['name', 'summary', 'details'];

            foreach ($contentFilters as $field) {
                if (!empty($filters['filters'][$field])) {
                    if (!$hasJoinedContent) {
                        $qb->join('wc.contents', 'wcc');
                        $qb->andWhere('wcc.locale = :locale');
                        $qb->setParameter('locale', $locale);
                        $hasJoinedContent = true;
                    }

                    $qb->andWhere(sprintf('wcc.%s LIKE :%s', $field, $field))
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
            ->select('COUNT(wc.id) as count')
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
