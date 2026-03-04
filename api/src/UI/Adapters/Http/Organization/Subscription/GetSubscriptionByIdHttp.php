<?php

namespace App\UI\Adapters\Http\Organization\Subscription;


use App\Domain\Organization\Subscription\GetSubscriptionByIdDTOInterface;
use Symfony\Component\Uid\Uuid;

final readonly class GetSubscriptionByIdHttp implements GetSubscriptionByIdDTOInterface
{
    public function __construct(
        private Uuid  $id,
        private ?Uuid $placeId,
        private ?Uuid $formulaId,
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPlaceId(): ?Uuid
    {
        return $this->placeId;
    }

    public function getFormulaId(): ?Uuid
    {
        return $this->formulaId;
    }
}
