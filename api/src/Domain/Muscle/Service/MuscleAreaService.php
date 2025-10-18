<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\MuscleArea;
use Symfony\Component\HttpFoundation\RequestStack;

class MuscleAreaService
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function enrichEntity(MuscleArea $muscleArea): MuscleArea
    {
        $locale  = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        $content = $muscleArea->getContentByLocale($locale);

        if ($content) {
            $muscleArea->setName($content->getName());
            $muscleArea->setSummary($content->getSummary());
            $muscleArea->setDetails($content->getDetails());
        }

        return $muscleArea;
    }
}
