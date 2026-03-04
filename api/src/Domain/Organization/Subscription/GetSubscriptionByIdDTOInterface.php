<?php

namespace App\Domain\Organization\Subscription;

use Symfony\Component\Uid\Uuid;

interface GetSubscriptionByIdDTOInterface
{
    public function getId(): Uuid;
}
