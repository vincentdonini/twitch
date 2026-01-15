<?php

namespace App\Infrastructure\Doctrine\Repository\Exercise;

use App\Domain\Exercise\Entity\Exercise;
use App\Domain\Exercise\Ports\ExerciseDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Doctrine\Sorts\DoctrineSortApplier;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class ExerciseRepository extends AbstractEntityRepository implements ExerciseDALInterface
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
        return Exercise::class;
    }

    public function getManager(): string
    {
        return Exercise::class;
    }

    public function getById(string $id): ?Exercise
    {
        return $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listExercises(
        int              $page = 1,
        int              $limit = 10,
        FilterCollection $filters = null,
        SortCollection   $sorts = null,
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

        // TODO MAYBE ???
        /*
        if (!$sorts->isEmpty()) {
            $joinedAliases = [];

            foreach ($sorts as $sort) {
                if (!$sort instanceof \App\Infrastructure\Sorts\SortValue) {
                    continue;
                }

                $field = $sort->field;
                $direction = $sort->direction->value;

                if ($field === 'contents.title') {
                    $relation = 'contents';
                    $alias = 'contents_0';

                    if (!isset($joinedAliases[$relation])) {
                        $qb->leftJoin(
                            "e.$relation",
                            $alias,
                            'WITH',
                            "$alias.locale = :locale"
                        );
                        $joinedAliases[$relation] = true;
                    }

                    $qb->addOrderBy("$alias.name", $direction);
                } else {
                    $qb->addOrderBy("e.$field", $direction);
                }
            }

            if (!empty($joinedAliases['contents'])) {
                $qb->setParameter('locale', $this->getLocale());
            }
        }
        */

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
