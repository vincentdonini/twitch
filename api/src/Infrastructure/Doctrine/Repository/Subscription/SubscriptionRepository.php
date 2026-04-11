<?php

namespace App\Infrastructure\Doctrine\Repository\Subscription;

use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Entity\Subscription;
use App\Domain\Organization\Enum\SubscriptionStatusEnum;
use App\Domain\Organization\Ports\SubscriptionDALInterface;
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

class SubscriptionRepository extends AbstractEntityRepository implements SubscriptionDALInterface
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
        return Subscription::class;
    }

    public function getManager(): string
    {
        return Subscription::class;
    }

    public function getById(Uuid $id): ?Subscription
    {
        return $this->find($id);
    }

    public function listByPlaceId(
        Uuid $placeId,
        int  $page = 1,
        int  $limit = RequestPaginator::DEFAULT_LIMIT,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('s')
            ->where('s.place = :placeId')
            ->setParameter('placeId', hex2bin(str_replace('-', '', (string)$placeId)))
            ->orderBy('s.createdAt', 'DESC');

        $aggQb = clone $qb;
        $count = (int)$aggQb->select('COUNT(s.id) as count')
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getSingleResult()['count'];

        $items = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return new LightPaginator($items, $count, $page, $limit);
    }

    public function listSubscriptionsByPlaceIdByFormulaId(
        Uuid              $placeId,
        Uuid              $formulaId,
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        $qb = $this->createQueryBuilder('s')
            ->where('s.place = :placeId')
            ->andWhere('s.formula = :formulaId')
            ->setParameter('placeId', hex2bin(str_replace('-', '', (string)$placeId)))
            ->setParameter('formulaId', hex2bin(str_replace('-', '', (string)$formulaId)));

        // Filters
        // -------------------------------------------------------------------------------------------------------------
//        if (!$filters->isEmpty()) {
//            $qb = (new DoctrineFilterApplier())
//                ->apply($qb, 's', $filters);
//        }

        // Sort
        // -------------------------------------------------------------------------------------------------------------
//        if (!$sorts->isEmpty()) {
//            $qb = (new DoctrineSortApplier())
//                ->apply($qb, 's', $sorts);
//        }

        // Pagination
        // -------------------------------------------------------------------------------------------------------------
        $aggQb     = clone $qb;
        $aggResult = $aggQb
            ->select('COUNT(s.id) as count')
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

    public function findActiveByUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->andWhere('s.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('statuses', [
                SubscriptionStatusEnum::ACTIVE,
                SubscriptionStatusEnum::PENDING,
            ])
            ->getQuery()
            ->getResult();
    }

    public function findExistingForUserAndFormula(
        User    $user,
        Formula $formula
    ): ?Subscription {
        return $this->createQueryBuilder('s')
            ->join('s.user', 'u')
            ->join('s.formula', 'f')
            ->andWhere('u.id = :userId')
            ->andWhere('f.id = :formulaId')
            ->andWhere('s.status IN (:statuses)')
            ->setParameter('userId', $user->getId()->toBinary())
            ->setParameter('formulaId', $formula->getId()->toBinary())
            ->setParameter('statuses', [
                SubscriptionStatusEnum::ACTIVE,
                SubscriptionStatusEnum::PENDING
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
