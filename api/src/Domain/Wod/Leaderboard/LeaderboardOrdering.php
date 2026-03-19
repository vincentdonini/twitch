<?php

namespace App\Domain\Wod\Leaderboard;

use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Entity\WodScore;
use LogicException;

final class LeaderboardOrdering
{
    private array $orderings;
    private bool  $timeWithRepsFallback;

    private function __construct(
        array $orderings,
        bool  $timeWithRepsFallback = false,
    ) {
        $this->orderings            = $orderings;
        $this->timeWithRepsFallback = $timeWithRepsFallback;
    }

    public function all(): array
    {
        return $this->orderings;
    }

    /**
     * Compares two WodScore entries according to this ordering.
     * Returns negative if $a is better (ranks higher), positive if $b is better.
     * Used for both deduplication (best per user) and final leaderboard sort.
     */
    public function compare(WodScore $a, WodScore $b): int
    {
        if ($this->timeWithRepsFallback) {
            $aFinished = $a->getTime() !== null;
            $bFinished = $b->getTime() !== null;

            // Finisher always beats DNF
            if ($aFinished !== $bFinished) {
                return $aFinished ? -1 : 1;
            }

            if ($aFinished) {
                // Both finished: lower time is better
                $cmp = $a->getTime() <=> $b->getTime();
            } else {
                // Both DNF: more repetitions is better
                $cmp = ($b->getRepetitions() ?? 0) <=> ($a->getRepetitions() ?? 0);
            }

            if ($cmp !== 0) {
                return $cmp;
            }

            // Tiebreaker: earliest performance wins
            return $a->getPerformedAt() <=> $b->getPerformedAt();
        }

        if (empty($this->orderings)) {
            // for-quality: keep most recent
            return $b->getPerformedAt() <=> $a->getPerformedAt();
        }

        $order     = $this->orderings[0];
        $field     = $order['field'];
        $direction = $order['direction'];

        $aValue = match ($field) {
            'time'        => $a->getTime(),
            'repetitions' => $a->getRepetitions(),
            'weight'      => $a->getWeight(),
            default       => null,
        };
        $bValue = match ($field) {
            'time'        => $b->getTime(),
            'repetitions' => $b->getRepetitions(),
            'weight'      => $b->getWeight(),
            default       => null,
        };

        // Null values are always ranked last
        if ($aValue === null && $bValue === null) {
            return $a->getPerformedAt() <=> $b->getPerformedAt();
        }
        if ($aValue === null) return 1;
        if ($bValue === null) return -1;

        $cmp = $aValue <=> $bValue;
        if ($direction === 'DESC') {
            $cmp = -$cmp;
        }

        if ($cmp !== 0) {
            return $cmp;
        }

        return $a->getPerformedAt() <=> $b->getPerformedAt();
    }

    public static function amrap(): self
    {
        return new self([
            ['field' => 'repetitions', 'direction' => 'DESC'],
        ]);
    }

    public static function emom(): self
    {
        return new self([
            ['field' => 'repetitions', 'direction' => 'DESC'],
        ]);
    }

    public static function forLoad(): self
    {
        return new self([
            ['field' => 'weight', 'direction' => 'DESC'],
        ]);
    }

    public static function forQuality(): self
    {
        return new self([]);
    }

    public static function forTime(): self
    {
        return new self(orderings: [], timeWithRepsFallback: true);
    }

    public static function tabata(): self
    {
        return new self([
            ['field' => 'repetitions', 'direction' => 'DESC'],
        ]);
    }

    public static function fromWod(Wod $wod, bool $hasTimeCap = false): self
    {
        // Any WOD with a time cap uses composite ordering:
        // finishers (time IS NOT NULL) ranked first, DNFs ranked by repetitions.
        if ($hasTimeCap) {
            return self::forTime();
        }

        return match ($wod->getWodType()->getSlug()) {
            'amrap'       => self::amrap(),
            'emom'        => self::emom(),
            'for-load'    => self::forLoad(),
            'for-quality' => self::forQuality(),
            'for-time'    => self::forTime(),
            'tabata'      => self::tabata(),
            default       => throw new LogicException(
                sprintf('No leaderboard ordering defined for WOD type "%s"', $wod->getWodType()->getSlug())
            ),
        };
    }
}
