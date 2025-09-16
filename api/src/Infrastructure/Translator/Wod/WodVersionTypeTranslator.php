<?php

namespace App\Infrastructure\Translator\Wod;

use App\Domain\Wod\Entity\WodVersionType;
use App\Domain\Wod\Service\WodVersionTypeTranslatorInterface;
use App\Shared\Utils\StringHelper;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class WodVersionTypeTranslator implements WodVersionTypeTranslatorInterface
{
    private static ?TranslatorInterface $translator = null;

    public function __construct(TranslatorInterface $translator)
    {
        self::$translator = $translator;
    }

    public function getName(WodVersionType $wodVersionType): string
    {
        if (!self::$translator) {
            throw new RuntimeException('Translator not set');
        }

        return self::$translator->trans(
            'version_type.' . StringHelper::kebabToSnake($wodVersionType->getSlug()) . '.name', [], 'wod'
        );
    }

    public function getDescription(WodVersionType $wodVersionType): string
    {
        if (!self::$translator) {
            throw new RuntimeException('Translator not set');
        }

        return self::$translator->trans(
            'version_type.' . StringHelper::kebabToSnake($wodVersionType->getSlug()) . '.description', [], 'wod'
        );
    }
}
