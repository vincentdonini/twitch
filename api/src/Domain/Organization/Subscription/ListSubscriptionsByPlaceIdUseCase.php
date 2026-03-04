<?php

namespace App\Domain\Organization\Subscription;

use App\Domain\Organization\Ports\SubscriptionDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

readonly class ListSubscriptionsByPlaceIdUseCase
{
    public function __construct(
        private SubscriptionDALInterface $subscriptionDAL,
    ) {
    }

    public function execute(ListSubscriptionsByPlaceIdDTOInterface $dto): LightPaginator
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            throw new InvalidArgumentException();
        }

        return $this->subscriptionDAL->listSubscriptionsByPlaceIdByFormulaId(
            placeId  : $dto->getPlaceId(),
            formulaId: $dto->getFormulaId(),
            page     : $dto->getPage(),
            limit    : $dto->getLimit(),
            filters  : $dto->getFilters(),
            sorts    : $dto->getSorts(),
        );
    }
}
