<?php

namespace App\Domain\Equipment\Service;

use App\Domain\Equipment\Entity\Equipment;
use Symfony\Component\HttpFoundation\RequestStack;

class EquipmentService
{
    public function __construct(
        private readonly RequestStack       $requestStack,
    ) {
    }

    public function enrichEntity(Equipment $equipment): Equipment
    {
        $locale  = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        $content = $equipment->getContentByLocale($locale);

        if ($content) {
            $equipment->setName($content->getName());
            $equipment->setSummary($content->getSummary());
            $equipment->setDetails($content->getDetails());
        }

        return $equipment;
    }
}
