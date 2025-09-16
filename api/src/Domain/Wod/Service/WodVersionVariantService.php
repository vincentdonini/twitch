<?php

namespace App\Domain\Wod\Service;

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

    public function enrichEntity(WodVersionVariant $wodVersionVariant): WodVersionVariant
    {
        $wodVersionVariant->setDescription(LocalizationHelper::getLocalizedValue($wodVersionVariant->getDescriptions(), $this->locale));

        return $wodVersionVariant;
    }
}
