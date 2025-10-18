<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\MuscleGroup;
use Symfony\Component\HttpFoundation\RequestStack;

class MuscleGroupService
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function enrichEntity(MuscleGroup $muscleGroup): MuscleGroup
    {
        $locale  = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        $content = $muscleGroup->getContentByLocale($locale);

        if ($content) {
            $muscleGroup->setName($content->getName());
            $muscleGroup->setSummary($content->getSummary());
            $muscleGroup->setDetails($content->getDetails());
        }

        return $muscleGroup;
    }
}
