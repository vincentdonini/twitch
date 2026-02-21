<?php

namespace App\Domain\Achievement\Service\Progress;

use App\Domain\Achievement\Entity\AchievementLevel;
use App\Domain\Achievement\Enum\AchievementLevelEnum;
use App\Domain\Achievement\Enum\AchievementSourceEnum;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Repository\Benchmark\BenchmarkRepository;
use App\Infrastructure\Doctrine\Repository\Benchmark\BenchmarkScoreRepository;

final readonly class BenchmarkAchievementCalculator implements AchievementProgressCalculatorInterface
{
    public function __construct(
        private BenchmarkRepository      $benchmarkRepository,
        private BenchmarkScoreRepository $benchmarkScoreRepository
    ) {
    }

    public function supports(AchievementSourceEnum $source, AchievementLevel $achievementLevel): bool
    {
        return $source === AchievementSourceEnum::tryFrom('benchmark');
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
            'FIRST_PR'           => $this->hasCompletedFirstPr($user),


            // ---------------------------------------------------------------------------------------------------------
            // FORCE_SKILLS
            // ---------------------------------------------------------------------------------------------------------

            // --- GYMNASTICS ------------------------------------------------------------------------------------------
            'PUSH_UPS'           => $this->getPerformanceBySlug(
                $user,
                'max-push-ups',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 40,
                    AchievementLevelEnum::GOLD->value   => 60,
                ],
                fn(BenchmarkScore $score) => $score->getRepetitions()
            ),
            'PULL_UPS'           => $this->getPerformanceBySlug(
                $user,
                'max-pull-ups',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 40,
                    AchievementLevelEnum::GOLD->value   => 60,
                ],
                fn(BenchmarkScore $score) => $score->getRepetitions()
            ),
            'MUSCLE_UPS'         => $this->getPerformanceBySlug(
                $user,
                'max-muscle-ups',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 25,
                    AchievementLevelEnum::GOLD->value   => 20,
                ],
                fn(BenchmarkScore $score) => $score->getRepetitions()
            ),
            'TOES_TO_BAR'        => $this->getPerformanceBySlug(
                $user,
                'max-toes-to-bar',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 25,
                    AchievementLevelEnum::GOLD->value   => 20,
                ],
                fn(BenchmarkScore $score) => $score->getRepetitions()
            ),
            'DOUBLE_UNDERS'      => $this->getPerformanceBySlug(
                $user,
                'max-double-unders',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 25,
                    AchievementLevelEnum::GOLD->value   => 20,
                ],
                fn(BenchmarkScore $score) => $score->getRepetitions()
            ),
            'LEGLESS_ROPE_CLIMB' => $this->getPerformanceBySlug(
                $user,
                'legless-rope-climb',
                $level,
                [
                    AchievementLevelEnum::DEFAULT->value => null,
                ],
                fn(BenchmarkScore $score) => $score->getRepetitions()
            ),

            // --- WEIGHTLIFTING ---------------------------------------------------------------------------------------
            'DEADLIFT_100_CLUB'  => $this->getPerformanceBySlug(
                $user,
                '1rm-deadlift',
                $level,
                [
                    AchievementLevelEnum::DEFAULT->value => 100,
                ],
                fn(BenchmarkScore $score) => $score->getWeight()
            ),
            'CLEAN_100_CLUB'     => $this->getPerformanceBySlug(
                $user,
                '1rm-squat-clean',
                $level,
                [
                    AchievementLevelEnum::DEFAULT->value => 100,
                ],
                fn(BenchmarkScore $score) => $score->getWeight()
            ),
            'JERK_100_CLUB'      => $this->getPerformanceBySlug(
                $user,
                '1rm-clean-jerk',
                $level,
                [
                    AchievementLevelEnum::DEFAULT->value => 100,
                ],
                fn(BenchmarkScore $score) => $score->getWeight()
            ),
            'SNATCH_100_CLUB'    => $this->getPerformanceBySlug(
                $user,
                '1rm-squat-snatch',
                $level,
                [
                    AchievementLevelEnum::DEFAULT->value => 100,
                ],
                fn(BenchmarkScore $score) => $score->getWeight()
            ),


            // ---------------------------------------------------------------------------------------------------------
            // FORCE_SKILLS
            // ---------------------------------------------------------------------------------------------------------

            // --- ROW -------------------------------------------------------------------------------------------------
            'ROW_100M'           => $this->getPerformanceBySlug(
                $user,
                'row-100m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 25,
                    AchievementLevelEnum::GOLD->value   => 20,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'ROW_250M'           => $this->getPerformanceBySlug(
                $user,
                'row-250m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 65,
                    AchievementLevelEnum::GOLD->value   => 50,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'ROW_500M'           => $this->getPerformanceBySlug(
                $user,
                'row-500m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 120,
                    AchievementLevelEnum::GOLD->value   => 105,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'ROW_1K'             => $this->getPerformanceBySlug(
                $user,
                'row-1k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 270,
                    AchievementLevelEnum::GOLD->value   => 240,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'ROW_2K'             => $this->getPerformanceBySlug(
                $user,
                'row-2k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 570,
                    AchievementLevelEnum::GOLD->value   => 510,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'ROW_3K'             => $this->getPerformanceBySlug(
                $user,
                'row-3k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 870,
                    AchievementLevelEnum::GOLD->value   => 780,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'ROW_5K'             => $this->getPerformanceBySlug(
                $user,
                'row-5k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 1500,
                    AchievementLevelEnum::GOLD->value   => 1320,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),

            // --- RUN -------------------------------------------------------------------------------------------------
            'RUN_100M'           => $this->getPerformanceBySlug(
                $user,
                'run-100m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 18,
                    AchievementLevelEnum::GOLD->value   => 14,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_200M'           => $this->getPerformanceBySlug(
                $user,
                'run-200m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 35,
                    AchievementLevelEnum::GOLD->value   => 28,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_400M'           => $this->getPerformanceBySlug(
                $user,
                'run-400m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 90,
                    AchievementLevelEnum::GOLD->value   => 75,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_800M'           => $this->getPerformanceBySlug(
                $user,
                'run-800m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 210,
                    AchievementLevelEnum::GOLD->value   => 180,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_1_MILE'         => $this->getPerformanceBySlug(
                $user,
                'run-1-mile',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 450,
                    AchievementLevelEnum::GOLD->value   => 390,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_2_MILES'        => $this->getPerformanceBySlug(
                $user,
                'run-2-miles',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 900,
                    AchievementLevelEnum::GOLD->value   => 750,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_5K'             => $this->getPerformanceBySlug(
                $user,
                'run-5k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 1680,
                    AchievementLevelEnum::GOLD->value   => 1500,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_10K'            => $this->getPerformanceBySlug(
                $user,
                'run-10k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 3600,
                    AchievementLevelEnum::GOLD->value   => 3000,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_HALF_MARATHON'  => $this->getPerformanceBySlug(
                $user,
                'run-half-marathon',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 9000,
                    AchievementLevelEnum::GOLD->value   => 7200,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'RUN_MARATHON'       => $this->getPerformanceBySlug(
                $user,
                'run-marathon',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 21600,
                    AchievementLevelEnum::GOLD->value   => 18000,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),

            // --- SWIM ------------------------------------------------------------------------------------------------
            'SWIM_50M'           => $this->getPerformanceBySlug(
                $user,
                'swim-50m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 60,
                    AchievementLevelEnum::GOLD->value   => 45,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_100M'          => $this->getPerformanceBySlug(
                $user,
                'swim-100m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 120,
                    AchievementLevelEnum::GOLD->value   => 90,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_200M'          => $this->getPerformanceBySlug(
                $user,
                'swim-200m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 300,
                    AchievementLevelEnum::GOLD->value   => 240,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_400M'          => $this->getPerformanceBySlug(
                $user,
                'swim-400m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 600,
                    AchievementLevelEnum::GOLD->value   => 510,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_500M'          => $this->getPerformanceBySlug(
                $user,
                'swim-500m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 720,
                    AchievementLevelEnum::GOLD->value   => 600,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_800M'          => $this->getPerformanceBySlug(
                $user,
                'swim-800m',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 1200,
                    AchievementLevelEnum::GOLD->value   => 1020,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_1K'            => $this->getPerformanceBySlug(
                $user,
                'swim-1k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 1500,
                    AchievementLevelEnum::GOLD->value   => 1320,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_1_5K'          => $this->getPerformanceBySlug(
                $user,
                'swim-1-5k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 2280,
                    AchievementLevelEnum::GOLD->value   => 2040,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_2K'            => $this->getPerformanceBySlug(
                $user,
                'swim-2k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 3000,
                    AchievementLevelEnum::GOLD->value   => 2700,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'SWIM_3K'            => $this->getPerformanceBySlug(
                $user,
                'swim-3k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 4500,
                    AchievementLevelEnum::GOLD->value   => 3900,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),

            // --- BIKE ------------------------------------------------------------------------------------------------
            'BIKE_1K'            => $this->getPerformanceBySlug(
                $user,
                'bike-1k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 180,
                    AchievementLevelEnum::GOLD->value   => 150,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'BIKE_5K'            => $this->getPerformanceBySlug(
                $user,
                'bike-5k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 720,
                    AchievementLevelEnum::GOLD->value   => 600,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'BIKE_10K'           => $this->getPerformanceBySlug(
                $user,
                'bike-10k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 000,
                    AchievementLevelEnum::GOLD->value   => 000,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'BIKE_20K'           => $this->getPerformanceBySlug(
                $user,
                'bike-20k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 1500,
                    AchievementLevelEnum::GOLD->value   => 1200,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'BIKE_30K'           => $this->getPerformanceBySlug(
                $user,
                'bike-30k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 4500,
                    AchievementLevelEnum::GOLD->value   => 3600,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'BIKE_50K'           => $this->getPerformanceBySlug(
                $user,
                'bike-50k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 7200,
                    AchievementLevelEnum::GOLD->value   => 6300,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),
            'BIKE_100K'          => $this->getPerformanceBySlug(
                $user,
                'bike-100k',
                $level,
                [
                    AchievementLevelEnum::BRONZE->value => null,
                    AchievementLevelEnum::SILVER->value => 16200,
                    AchievementLevelEnum::GOLD->value   => 14400,
                ],
                fn(BenchmarkScore $score) => $score->getTime()
            ),

            // --- BENCHMARKS ------------------------------------------------------------------------------------------
            'BENCHMARKS'         => $this->getBenchmarkCompletionProgress($user),

            default              => throw new \LogicException(
                sprintf(
                    'Unsupported achievement "%s"',
                    $achievementCode
                )
            ),
        };
    }

    private function hasCompletedFirstPr(User $user): float
    {
        return $this->benchmarkScoreRepository->count([
            'user' => $user,
        ]);
    }

    private function getPerformanceBySlug(
        User                 $user,
        string               $slug,
        AchievementLevelEnum $level,
        array                $thresholds,
        callable             $valueExtractor
    ): float {
        /* @var Benchmark $benchmark */
        $benchmark = $this->benchmarkRepository->findOneBy(['slug' => $slug]);

        if (!$benchmark) {
            throw new \RuntimeException(sprintf('Benchmark "%s" not found', $slug));
        }

        /* @var BenchmarkScore $benchmarkScore */
        $benchmarkScore = $this->benchmarkScoreRepository->findOneBy([
            'user'      => $user,
            'benchmark' => $benchmark,
        ]);

        if (!$benchmarkScore) {
            return 0.0;
        }

        if ($level === AchievementLevelEnum::BRONZE || $level === AchievementLevelEnum::DEFAULT) {
            return 1.0;
        }

        $value  = $valueExtractor($benchmarkScore);
        $target = $thresholds[$level->value] ?? null;

        if ($target === null || $target <= 0 || $value <= 0) {
            return 0.0;
        }

        if ($value >= $target) {
            return 1.0;
        }

        $progress = $value / $target;

        return floor(max(0.0, min(1.0, $progress)) * 100) / 100;
    }

    private function getBenchmarkCompletionProgress(User $user): float
    {
        $totalBenchmarks = $this->benchmarkRepository->countBenchmarks();

        if ($totalBenchmarks === 0) {
            return 0.0;
        }

        $completedBenchmarks = $this->benchmarkScoreRepository->countDistinctByUser($user);

        $progress = $completedBenchmarks / $totalBenchmarks;

        return (float)max(0.0, min(1.0, round($progress, 2)));
    }
}
