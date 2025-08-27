<?php

namespace App\Infrastructure\Translator\Movement;

use App\Domain\Movement\Service\MovementTranslatorInterface;
use App\Domain\Movement\Entity\Movement;
use App\Shared\Utils\StringHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class MovementTranslator implements MovementTranslatorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {

    }

    public function getName(Movement $movement): string
    {
        return $this->translator->trans('movement.' . StringHelper::kebabToSnake($movement->getSlug()) . '.name');
    }

    public function getDescription(Movement $movement): string
    {
        return $this->translator->trans('movement.' . StringHelper::kebabToSnake($movement->getSlug()) . '.description');
    }
}
