<?php

namespace App\Domain\Achievement\Service\Progress;

use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Enum\AchievementLevelEnum;
use App\Domain\Achievement\Enum\AchievementSourceEnum;
use App\Domain\Common\Service\FrenchHolidayService;
use App\Domain\User\Entity\User;
use App\Domain\Wod\Entity\WodScore;
use App\Infrastructure\Doctrine\Repository\Wod\WodRepository;
use App\Infrastructure\Doctrine\Repository\Wod\WodScoreRepository;

final readonly class WodAchievementCalculator implements AchievementProgressCalculatorInterface
{
    public function __construct(
        private WodRepository        $wodRepository,
        private WodScoreRepository   $wodScoreRepository,
        private FrenchHolidayService $frenchHolidayService,
    ) {
    }

    public function supports(AchievementSourceEnum $source, AchievementLevel $achievementLevel): bool
    {
        return $source === AchievementSourceEnum::tryFrom('wod');
    }

    public function calculate(User $user, AchievementSourceEnum $source, AchievementLevel $achievementLevel): float
    {
        $achievementCode = $achievementLevel->getAchievement()->getCode();
        $level           = $achievementLevel->getLevel();

        $percentageDone = $this->getPercentageDone($user, $achievementCode, $level);

        return min($percentageDone * 100, 100);
    }

    private function getPercentageDone(User $user, string $achievementCode, AchievementLevelEnum $level): float
    {
        return match ($achievementCode) {

            // ---------------------------------------------------------------------------------------------------------
            // FIRST_STEPS_MILESTONES
            // ---------------------------------------------------------------------------------------------------------

            // --- FIRSTS ----------------------------------------------------------------------------------------------
            'FIRST_WOD'          => $this->hasCompletedFirstWod($user),
            'FIRST_RX'           => $this->hasCompletedFirstRx($user),
            'FIRST_SCALED_TO_RX' => $this->hasCompletedFirstScaledToRx($user),

            // --- COUNTS ----------------------------------------------------------------------------------------------
            'WODS_10'            => $this->countCompletedWods($user, 10),
            'WODS_25'            => $this->countCompletedWods($user, 25),
            'WODS_50'            => $this->countCompletedWods($user, 50),
            'WODS_100'           => $this->countCompletedWods($user, 100),
            'WODS_250'           => $this->countCompletedWods($user, 250),
            'WODS_500'           => $this->countCompletedWods($user, 500),


            // ---------------------------------------------------------------------------------------------------------
            // REGULARITY_DISCIPLINE
            // ---------------------------------------------------------------------------------------------------------

            // --- STREAKS ---------------------------------------------------------------------------------------------
            'NO_REST_WEEK'       => $this->hasDailyConsistency($user, 7),
            'TWO_A_DAY'          => $this->hasMultipleWodsInSameDay($user, 2),
            'FIVE_DAYS_STRONG'   => $this->hasDailyConsistency($user, 5),

            // --- TIME BASED ------------------------------------------------------------------------------------------
            'EARLY_BIRD'         => $this->hasCompletedWodsBetweenTime($user, '05:00', '08:00'),
            'LUNCH_BREAK_HERO'   => $this->hasCompletedWodsBetweenTime($user, '12:00', '14:00'),
            'NIGHT_OWL'          => $this->hasCompletedWodsAfterTime($user, '21:00'),

            // --- WEEKEND ---------------------------------------------------------------------------------------------
            'WEEKEND_WARRIOR'    => $this->hasWeekEndWods($user),

            // --- HABITS & CONSISTENCY --------------------------------------------------------------------------------
            'NO_EXCUSES'         => $this->hasWodOnFrenchHoliday($user),
            'BACK_AFTER_BREAK'   => $this->backAfterBreak($user),
            'CONSISTENCY_KING'   => $this->hasWeeklyConsistency($user, 8, 3),


            // ---------------------------------------------------------------------------------------------------------
            // FORCE_SKILLS
            // ---------------------------------------------------------------------------------------------------------

            // --- CARDIO ----------------------------------------------------------------------------------------------
            'BURPEES_LOVER'      => $this->hasWodCompletedByName($user, "Burpees lover"),

            'FRAN_UNDER_5'       => $this->hasFranUnder5($user),
            'MURPH_COMPLETED'    => $this->hasWodCompletedByName($user, 'Murph'),
            'TEAM_PLAYER'        => $this->hasTeamPlayer($user),

            // --- OFFICIAL --------------------------------------------------------------------------------------------
            'GIRLS'              => $this->getWodCategoryCompletionProgress($user, 'the-girls'),
            'HEROES'             => $this->getWodCategoryCompletionProgress($user, 'the-heroes'),

            // --- OPEN ------------------------------------------------------------------------------------------------
            'OPEN_2011'          => $this->openAchievement($user, 'OPEN 11.'),
            'OPEN_2012'          => $this->openAchievement($user, 'OPEN 12.'),
            'OPEN_2013'          => $this->openAchievement($user, 'OPEN 13.'),
            'OPEN_2014'          => $this->openAchievement($user, 'OPEN 14.'),
            'OPEN_2015'          => $this->openAchievement($user, 'OPEN 15.'),
            'OPEN_2016'          => $this->openAchievement($user, 'OPEN 16.'),
            'OPEN_2017'          => $this->openAchievement($user, 'OPEN 17.'),
            'OPEN_2018'          => $this->openAchievement($user, 'OPEN 18.'),
            'OPEN_2019'          => $this->openAchievement($user, 'OPEN 19.'),
            'OPEN_2020'          => $this->openAchievement($user, 'OPEN 20.'),
            'OPEN_2021'          => $this->openAchievement($user, 'OPEN 21.'),
            'OPEN_2022'          => $this->openAchievement($user, 'OPEN 22.'),
            'OPEN_2023'          => $this->openAchievement($user, 'OPEN 23.'),
            'OPEN_2024'          => $this->openAchievement($user, 'OPEN 24.'),
            'OPEN_2025'          => $this->openAchievement($user, 'OPEN 25.'),

            default              => throw new \LogicException(
                sprintf(
                    'Unsupported achievement "%s"',
                    $achievementCode
                )
            ),
        };
    }

    private function hasCompletedFirstWod(User $user): float
    {
        return $this->wodScoreRepository->count([
            'user' => $user,
        ]) > 0 ? 1 : 0;
    }

    private function hasCompletedFirstRx(User $user): float
    {
        return $this->wodScoreRepository->count([
            'user' => $user,
        ]) > 0 ? 1.0 : 0.0;
    }

    private function hasCompletedFirstScaledToRx(User $user): float
    {
        return $this->wodScoreRepository->hasScaledToRx($user) > 0 ? 1.0 : 0.0;
    }

    private function countCompletedWods(User $user, int $threshold): float
    {
        if ($threshold <= 0) {
            return 0.0;
        }

        $count = $this->wodScoreRepository->count([
            'user' => $user,
        ]);

        $progress = $count / $threshold;

        return round(max(0.0, min(1.0, $progress)), 2);
    }

    private function hasCompletedWodsBetweenTime(User $user, string $start, string $end): float
    {
        return $this->calculateWodCountByTime($user, [
            'operator' => 'BETWEEN',
            'value'    => [$start, $end],
        ]) > 0 ? 1.0 : 0.0;
    }

    private function hasCompletedWodsAfterTime(User $user, string $time): float
    {
        return $this->calculateWodCountByTime($user, [
            'operator' => 'AFTER',
            'value'    => $time,
        ]) > 0 ? 1.0 : 0.0;
    }

    private function hasWodOnFrenchHoliday(User $user): float
    {
        $scores = $this->wodScoreRepository->findBy(['user' => $user]);
        foreach ($scores as $score) {
            if ($this->frenchHolidayService->isFrenchHoliday($score->getPerformedAt())) {
                return 1.0;
            }
        }

        return 0.0;
    }

    private function hasWeekEndWods(User $user): float
    {
        return $this->wodScoreRepository->countWeekEndWods($user) > 0 ? 1.0 : 0.0;
    }

    private function calculateWodCountByTime(User $user, array $criteria): float
    {
        if (!isset($criteria['operator'], $criteria['value'])) {
            throw new \InvalidArgumentException('Time criteria must contain operator and value.');
        }

        $operator = strtoupper($criteria['operator']);

        $toSeconds = static function (string $time): int {
            [$h, $m] = explode(':', $time);
            return ((int)$h * 3600) + ((int)$m * 60);
        };

        $scores = $this->wodScoreRepository->findBy(['user' => $user]);

        $count = 0;

        foreach ($scores as $score) {
            $performedAt = $score->getPerformedAt();

            $currentSeconds =
                ((int)$performedAt->format('H') * 3600) +
                ((int)$performedAt->format('i') * 60);

            switch ($operator) {

                case 'BETWEEN':
                    if (!is_array($criteria['value']) || count($criteria['value']) !== 2) {
                        throw new \InvalidArgumentException('BETWEEN requires an array of two times.');
                    }

                    [$start, $end] = array_map($toSeconds, $criteria['value']);

                    // plage normale ou traversant minuit
                    if (
                        ($start <= $end && $currentSeconds >= $start && $currentSeconds <= $end) ||
                        ($start > $end && ($currentSeconds >= $start || $currentSeconds <= $end))
                    ) {
                        $count++;
                    }
                    break;

                case 'BEFORE':
                    $limit = $toSeconds($criteria['value']);
                    if ($currentSeconds <= $limit) {
                        $count++;
                    }
                    break;

                case 'AFTER':
                    $limit = $toSeconds($criteria['value']);
                    if ($currentSeconds >= $limit) {
                        $count++;
                    }
                    break;

                default:
                    throw new \LogicException(sprintf('Unsupported time operator "%s"', $operator));
            }
        }

        return (float)$count;
    }

    private function backAfterBreak(User $user, int $minDays = 14): float
    {
        $scores = $this->wodScoreRepository->findBy(['user' => $user]);

        if (count($scores) < 2) {
            return 0.0;
        }

        $last     = $scores[0]->getPerformedAt();
        $previous = $scores[1]->getPerformedAt();

        $diffDays = $last->diff($previous)->days;

        return $diffDays >= $minDays ? 1.0 : 0.0;
    }

    private function hasMultipleWodsInSameDay(User $user, int $minPerDay = 2): float
    {
        /* @var WodScore[] $wodScores */
        $wodScores = $this->wodScoreRepository->getByUser($user);

        if (!$wodScores) {
            return 0.0;
        }

        $days = [];

        foreach ($wodScores as $wodScore) {
            $day        = $wodScore->getPerformedAt()->format('Y-m-d');
            $days[$day] = ($days[$day] ?? 0) + 1;

            if ($days[$day] >= $minPerDay) {
                return 1.0;
            }
        }

        return 0.0;
    }

    private function hasDailyConsistency(User $user, int $minPerDay): float
    {
        $scores = $this->wodScoreRepository->getByUser($user);

        if (!$scores) {
            return 0.0;
        }

        $days = [];

        foreach ($scores as $score) {
            $day        = $score->getPerformedAt()->format('Y-m-d');
            $days[$day] = ($days[$day] ?? 0) + 1;
        }

        $streak     = 0;
        $currentDay = new \DateTimeImmutable('today');

        while (
            isset($days[$currentDay->format('Y-m-d')]) &&
            $days[$currentDay->format('Y-m-d')] >= $minPerDay
        ) {
            $streak++;
            $currentDay = $currentDay->modify('-1 day');
        }

        return (float)$streak;
    }

    private function hasWeeklyConsistency(User $user, int $numberOfweek, float $minPerWeek): int
    {
        /* @var WodScore[] $wodScores */
        $wodScores = $this->wodScoreRepository->getByUser($user, 'ASC');

        if (count($wodScores) < $numberOfweek * $minPerWeek) {
            return 0.0;
        }

        $weeks = [];

        foreach ($wodScores as $wodScore) {
            $date                  = $wodScore->getPerformedAt();
            $yearWeekIndex         = $date->format('oW'); // o = year ISO, W = week number
            $weeks[$yearWeekIndex] = ($weeks[$yearWeekIndex] ?? 0) + 1;
        }

        $weekNumbers = array_keys($weeks);
        sort($weekNumbers);

        $consecutive = 0;

        for ($i = 0; $i < count($weekNumbers); $i++) {
            if ($weeks[$weekNumbers[$i]] >= $minPerWeek) {
                $consecutive++;
                if ($consecutive >= $numberOfweek) {
                    return 1.0;
                }
            } else {
                $consecutive = 0;
            }
        }

        return 0.0;
    }

    private function hasFranUnder5(User $user): float
    {
        $thresholdMinutes = 300;

        /* @var WodScore[] $wodScores */
        $wodScores = $this->wodScoreRepository->getByUserAndName($user, 'Fran');

        return count(array_filter($wodScores, fn(WodScore $wodScore) => $wodScore->getTime() < $thresholdMinutes)) > 0 ? 1.0 : 0.0;
    }

    private function hasWodCompletedByName(User $user, string $name): float
    {
        return $this->wodScoreRepository->countByUserAndName($user, $name) > 0 ? 1.0 : 0.0;
    }

    private function getWodCategoryCompletionProgress(User $user, string $categorySlug): float
    {
        $totalWods = $this->wodRepository->countByWodCategorySlug($categorySlug);

        if ($totalWods === 0) {
            return 0.0;
        }

        $completedWods = $this->wodScoreRepository->countDistinctByUserAndWodCategorySlug($user, $categorySlug);

        $progress = $completedWods / $totalWods;

        return (float)max(0.0, min(1.0, round($progress, 2)));
    }

    private function hasTeamPlayer(User $user): float
    {
        return $this->wodScoreRepository->countTeamScoresByUser($user) > 0 ? 1.0 : 0.0;
    }

    private function openAchievement(User $user, string $openName): float
    {
        $total     = $this->wodRepository->countByName($openName);
        $completed = $this->wodScoreRepository->countDistinctByUserAndName($user, $openName);

        if ($total === 0 || $completed === 0) {
            return 0.0;
        }

        $progress = $completed / $total;

        return (float)max(0.0, min(1.0, round((float)$progress, 2)));
    }
}
