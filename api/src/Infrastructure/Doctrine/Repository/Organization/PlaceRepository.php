<?php

namespace App\Infrastructure\Doctrine\Repository\Organization;

use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Ports\PlaceDALInterface;
use App\Domain\User\Entity\User;
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

class PlaceRepository extends AbstractEntityRepository implements PlaceDALInterface
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
        return Place::class;
    }

    public function getManager(): string
    {
        return Place::class;
    }

    public function getById(Uuid $id): ?Place
    {
        return $this->find($id);
    }

    public function listPlaces(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('p');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())
                ->apply($qb, 'p', $filters);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            $qb = (new DoctrineSortApplier())
                ->apply($qb, 'p', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(p.id) as count')
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

    public function findByCoach(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId()->toBinary())
            ->getQuery()
            ->getResult();
    }

    public function countPlaces(): int
    {
        return (int)$this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
