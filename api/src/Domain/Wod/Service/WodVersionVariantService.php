<?php

namespace App\Domain\Wod\Service;

use App\Application\Wod\DTO\WodVersionVariantDTO;
use App\Domain\Wod\Entity\WodVersionVariant;
use App\Shared\Utils\LocalizationHelper;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodVersionVariantService
{
    private string $locale;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
        $this->locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'en';
    }

    public function transformToDTO(WodVersionVariant $wodVersionVariant): WodVersionVariantDTO
    {
        return new WodVersionVariantDTO(
            $wodVersionVariant->getId(),
            $wodVersionVariant->getGender(),
            $wodVersionVariant->getRounds(),
            $wodVersionVariant->getTimeCap(),
            LocalizationHelper::getLocalizedValue($wodVersionVariant->getDescription(), $this->locale),
            $wodVersionVariant->getWodVersion()->getId(),
        );
    }
}
