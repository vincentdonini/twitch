<?php

namespace App\Application\User\DTO;

use App\Domain\Organization\Entity\Subscription;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class UserMeGymSubscriptionDTO
{
    #[Groups([FrontGroupsEnum::USER_ME])]
    public string $subscriptionId;

    #[Groups([FrontGroupsEnum::USER_ME])]
    public string $placeId;

    #[Groups([FrontGroupsEnum::USER_ME])]
    public string $placeName;

    public static function fromSubscription(Subscription $subscription): self
    {
        $dto                 = new self();
        $dto->subscriptionId = (string) $subscription->getId();
        $dto->placeId        = (string) $subscription->getPlace()->getId();
        $dto->placeName      = $subscription->getPlace()->getName();

        return $dto;
    }
}
