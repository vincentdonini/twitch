<?php

namespace App\Infrastructure\Doctrine\Repository\Security;

use App\Domain\Security\Entity\Role;
use App\Domain\Security\Ports\RoleDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Doctrine\Sorts\DoctrineSortApplier;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class RoleRepository extends AbstractEntityRepository implements RoleDALInterface
{
    use LocaleTrait;

    public function __construct(
        private readonly string       $locale,
        protected ManagerRegistry     $em,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($em);
    }

    public function getClass(): string
    {
        return Role::class;
    }

    public function getManager(): string
    {
        return Role::class;
    }

    public function getById(string $id): ?Role
    {
        return $this->createQueryBuilder('r')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listRoles(
        int              $page = 1,
        int              $limit = 10,
        FilterCollection $filters = null,
        SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('r');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
//        if (!$filters->isEmpty()) {
//            (new DoctrineFilterApplier())->apply($qb, 'r', $filters);
//        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
//        if (!$sorts->isEmpty()) {
//            (new DoctrineSortApplier())->apply($qb, 'r', $sorts);
//        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(DISTINCT r.id) as count')
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
