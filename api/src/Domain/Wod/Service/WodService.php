<?php

namespace App\Domain\Wod\Service;

use App\Domain\Movement\Service\MovementService;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodVersion;
use App\Domain\Wod\Entity\WodVersionVariant;
use App\Domain\Wod\Entity\WodVersionVariantMovement;
use App\Shared\Utils\LocalizationHelper;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodService
{
    private string $locale;

    public function __construct(
        private readonly RequestStack       $requestStack,
        private readonly WodTypeService     $wodTypeService,
        private readonly WodCategoryService $wodCategoryService,
        private readonly MovementService    $movementService,
    ) {
        $this->locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'en';
    }

    public function enrichEntity(Wod $wod): Wod
    {
        $wod->setDescription(
            LocalizationHelper::getLocalizedValue($wod->getDescriptions(), $this->locale)
        );

        if ($wod->getWodType()) {
            $wod->setWodType($this->wodTypeService->enrichEntity($wod->getWodType()));
        }

        if ($wod->getWodCategory()) {
            $wod->setWodCategory($this->wodCategoryService->enrichEntity($wod->getWodCategory()));
        }

        foreach ($wod->getWodVersions() as $version) {
            $this->enrichWodVersion($version);
        }

        return $wod;
    }

    private function enrichWodVersion(WodVersion $version): void
    {
        foreach ($version->getWodVersionVariants() as $variant) {
            $this->enrichWodVersionVariant($variant);
        }
    }

    private function enrichWodVersionVariant(WodVersionVariant $variant): void
    {
        $variant->setDescription(
            LocalizationHelper::getLocalizedValue($variant->getDescriptions(), $this->locale)
        );

        foreach ($variant->getWodVersionVariantMovements() as $variantMovement) {
            $this->enrichWodVersionVariantMovement($variantMovement);
        }
    }

    private function enrichWodVersionVariantMovement(WodVersionVariantMovement $variantMovement): void
    {
        $variantMovement->setMovement($this->movementService->enrichEntity($variantMovement->getMovement()));
    }
}
