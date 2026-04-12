<?php

namespace App\Domain\Wod\WodVariant;

use Symfony\Component\Uid\Uuid;

interface DeleteWodVariantDTOInterface
{
    public function getVariantId(): Uuid;
}
