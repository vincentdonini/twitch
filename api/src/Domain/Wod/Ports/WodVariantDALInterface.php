<?php

namespace App\Domain\Wod\Ports;

use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodAgeRange;
use App\Domain\Wod\Entity\WodDivision;
use App\Domain\Wod\Entity\WodVariant;
use App\Domain\Wod\Enum\GenderEnum;
use Symfony\Component\Uid\Uuid;

interface WodVariantDALInterface
{
    public function getById(Uuid $id): ?WodVariant;

    public function getByWodIdAndWodDivisionId(int $wodId, int $wodDivisionId): ?WodVariant;
    
    public function getByCriteria(
        Wod          $wod,
        WodDivision  $division,
        ?WodAgeRange $ageRange,
        ?GenderEnum  $gender
    ): ?WodVariant;
}
