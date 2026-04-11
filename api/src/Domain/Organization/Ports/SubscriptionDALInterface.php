<?php

namespace App\Domain\Organization\Ports;

use App\Domain\Organization\Entity\Formula;
use App\Domain\Organization\Entity\Subscription;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

interface SubscriptionDALInterface
{
    public function getById(Uuid $id): ?Subscription;

    public function listByPlaceId(
        Uuid              $placeId,
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
    ): LightPaginator;

    public function listSubscriptionsByPlaceIdByFormulaId(
        Uuid              $placeId,
        Uuid              $formulaId,
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null
    ): LightPaginator;

    public function findExistingForUserAndFormula(User $user, Formula $formula): ?Subscription;

    /** @return Subscription[] */
    public function findActiveByUser(User $user): array;
}
