<?php

namespace App\Infrastructure\Doctrine\Repository\Security;

use App\Domain\Security\Entity\Permission;
use App\Domain\Security\Ports\PermissionDALInterface;
use App\Infrastructure\Doctrine\Filters\DoctrineFilterApplier;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Doctrine\Sorts\DoctrineSortApplier;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class PermissionRepository extends AbstractEntityRepository implements PermissionDALInterface
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
        return Permission::class;
    }

    public function getManager(): string
    {
        return Permission::class;
    }

    public function getById(string $id): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function listPermissions(
        int              $page = 1,
        int              $limit = 10,
        FilterCollection $filters = null,
        SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('p');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
//        if (!$filters->isEmpty()) {
//            (new DoctrineFilterApplier())->apply($qb, 'p', $filters);
//        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
//        if (!$sorts->isEmpty()) {
//            (new DoctrineSortApplier())->apply($qb, 'p', $sorts);
//        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(DISTINCT p.id) as count')
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

    public function listPermissionsByRole(
        string           $roleId,
        int              $page = 1,
        int              $limit = 10,
        FilterCollection $filters = null,
        SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('p')
            ->join('p.roles', 'r')
            ->where('r.id = :roleId')
            ->setParameter('roleId', $roleId);

        // Filters
        // -------------------------------------------------------------------------------------------------------------
//        if (!$filters->isEmpty()) {
//            (new DoctrineFilterApplier())->apply($qb, 'p', $filters);
//        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
//        if (!$sorts->isEmpty()) {
//            (new DoctrineSortApplier())->apply($qb, 'p', $sorts);
//        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(DISTINCT p.id) as count')
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
