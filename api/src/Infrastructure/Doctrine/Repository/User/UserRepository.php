<?php

namespace App\Infrastructure\Doctrine\Repository\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Doctrine\Repository\AbstractEntityRepository;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class UserRepository extends AbstractEntityRepository implements UserDALInterface
{
    use LocaleTrait;

    public function __construct(
        protected ManagerRegistry     $em,
        private readonly RequestStack $requestStack,
    ) {
        parent::__construct($em);
    }

    public function getClass(): string
    {
        return User::class;
    }

    public function getManager(): string
    {
        return User::class;
    }

    public function getById(Uuid $id): ?User
    {
        return $this->find($id);
    }

    public function listUsers(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('u');
        if (!empty($filters['filters'])) {

            // User
            // ---------------------------------------------------------------------------------------------------------
            if (!empty($filters['filters']['slug'])) {
                $qb
                    ->andWhere('u.slug LIKE :slug')
                    ->setParameter(
                        'slug',
                        sprintf(
                            '%%%s%%',
                            $filters['filters']['slug']
                        )
                    );
            }
        }

        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(u.id) as count')
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

    public function countUsers(): int
    {
        return (int)$this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
