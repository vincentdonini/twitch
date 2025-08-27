<?php

namespace App\Domain\Wod\Service;

use App\Application\Wod\DTO\WodVersionDTO;
use App\Domain\Wod\Entity\WodVersion;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodVersionService
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
        $this->locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'en';
    }

    public function transformToDTO(WodVersion $wodVersion): WodVersionDTO
    {
        return new WodVersionDTO(
            $wodVersion->getId(),
            $wodVersion->getWod()->getId(),
            $wodVersion->getWodVersionType()->getId(),
        );
    }
}
