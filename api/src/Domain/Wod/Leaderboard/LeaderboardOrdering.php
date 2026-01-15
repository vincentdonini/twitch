<?php

namespace App\Domain\Wod\Leaderboard;

use App\Domain\Wod\Entity\Wod;
use LogicException;

final class LeaderboardOrdering
{
    private array $orderings;

    private function __construct(
        array $orderings
    ) {
        $this->orderings = $orderings;
    }

    public function all(): array
    {
        return $this->orderings;
    }

    public static function amrap(): self
    {
        return new self([
            [
                'field'     => 'repetitions',
                'direction' => 'DESC',
            ],
        ]);
    }

    public static function emom(): self
    {
        return new self([
            [
                'field'     => 'repetitions',
                'direction' => 'DESC',
            ],
        ]);
    }

    public static function forLoad(): self
    {
        return new self([
            [
                'field'     => 'weight',
                'direction' => 'DESC',
            ],
        ]);
    }

    public static function forQuality(): self
    {
        return new self([

        ]);
    }

    public static function forTime(): self
    {
        return new self([
            [
                'field'     => 'time',
                'direction' => 'ASC',
            ],
        ]);
    }

    public static function tabata(): self
    {
        return new self([
            [
                'field'     => 'repetitions',
                'direction' => 'DESC',
            ],
        ]);
    }

    public static function fromWod(Wod $wod): self
    {
        return match ($wod->getWodType()->getSlug()) {
            'amrap'       => self::amrap(),
            'emom'        => self::emom(),
            'for-load'    => self::forLoad(),
            'for-quality' => self::forQuality(),
            'for-time'    => self::forTime(),
            'tabata'      => self::tabata(),
            default       => throw new LogicException(
                sprintf(
                    'No leaderboard ordering defined for WOD type "%s"',
                    $wod->getWodType()->getSlug()
                )
            ),
        };
    }
}
