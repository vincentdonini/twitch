<?php

namespace App\Domain\Muscle\Service;

use App\Domain\Muscle\Entity\Muscle;
use Symfony\Component\HttpFoundation\RequestStack;

class MuscleService
{
    public function __construct(
        private readonly RequestStack       $requestStack,
        private readonly MuscleGroupService $muscleGroupService,
        private readonly MuscleAreaService  $muscleAreaService,
    ) {
    }

    public function enrichEntity(Muscle $muscle): Muscle
    {
        $locale  = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
        $content = $muscle->getContentByLocale($locale);

        if ($content) {
            $muscle->setName($content->getName());
            $muscle->setSummary($content->getSummary());
            $muscle->setDetails($content->getDetails());
        }

        // Area
        $muscle->setArea($this->muscleAreaService->enrichEntity($muscle->getArea()));

        // Group
        $group = $muscle->getGroup();
        if ($group !== null) {
            $muscle->setGroup($this->muscleGroupService->enrichEntity($group));
        }

        return $muscle;
    }
}
