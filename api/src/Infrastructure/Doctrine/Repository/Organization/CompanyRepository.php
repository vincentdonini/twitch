<?php

namespace App\Infrastructure\Doctrine\Repository\Organization;

use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Ports\CompanyDALInterface;
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

class CompanyRepository extends AbstractEntityRepository implements CompanyDALInterface
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
        return Company::class;
    }

    public function getManager(): string
    {
        return Company::class;
    }

    public function getById(Uuid $id): ?Company
    {
        return $this->find($id);
    }

    public function listCompanies(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('c');

        // Filters
        // -------------------------------------------------------------------------------------------------------------
        if (!$filters->isEmpty()) {
            $qb = (new DoctrineFilterApplier())
                ->apply($qb, 'c', $filters);
        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
        if (!$sorts->isEmpty()) {
            $qb = (new DoctrineSortApplier())
                ->apply($qb, 'c', $sorts);
        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(c.id) as count')
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

    public function findByOwner(User $user): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.owners', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId()->toBinary())
            ->getQuery()
            ->getResult();
    }
}
