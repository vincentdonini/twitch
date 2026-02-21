<?php

namespace App\Domain\Achievement\Service\Progress;

use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Enum\AchievementSourceEnum;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class CalculatorRegistry
{
    /** @var AchievementProgressCalculatorInterface[] */
    private array $calculators;

    public function __construct(
        #[TaggedIterator('app.achievement_progress_calculator')]
        iterable $calculators
    ) {
        $this->calculators = $calculators instanceof \Traversable
            ? iterator_to_array($calculators)
            : $calculators;
    }

    public function getCalculator(AchievementSourceEnum $source, AchievementLevel $achievementLevel): AchievementProgressCalculatorInterface
    {
        foreach ($this->calculators as $calculator) {
            if ($calculator->supports($source, $achievementLevel)) {
                return $calculator;
            }
        }

        throw new \LogicException('No calculator found for achievement level');
    }
}
