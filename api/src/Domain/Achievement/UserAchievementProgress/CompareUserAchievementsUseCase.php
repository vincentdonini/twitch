<?php

namespace App\Domain\Achievement\UserAchievementProgress;

use App\Application\Achievement\Comparison\AchievementComparisonDTO;
use App\Application\Achievement\Comparison\CategoryComparisonDTO;
use App\Application\Achievement\Comparison\CompareUserAchievementsResponseDTO;
use App\Application\Achievement\Comparison\GroupComparisonDTO;
use App\Application\Achievement\Comparison\ScoreComparisonDTO;
use App\Application\Achievement\Comparison\ScoreDetailDTO;
use App\Application\Achievement\Comparison\SummaryComparisonDTO;
use App\Application\Achievement\Comparison\UserComparisonDTO;
use App\Domain\Achievement\Enum\AchievementComparisonStatusEnum;
use App\Domain\Achievement\Enum\ComparisonWinnerEnum;
use App\Domain\Achievement\Ports\AchievementDALInterface;
use App\Domain\Achievement\Ports\UserAchievementProgressDALInterface;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\User\Entity\User;
use App\Domain\User\Ports\UserDALInterface;

final readonly class CompareUserAchievementsUseCase
{
    public function __construct(
        private UserDALInterface                    $userDAL,
        private AchievementDALInterface             $achievementDAL,
        private UserAchievementProgressDALInterface $userAchievementProgressDAL,
    ) {
    }

    public function execute(CompareUserAchievementsDTOInterface $dto): CompareUserAchievementsResponseDTO
    {
        $user  = $this->userDAL->getById($dto->getUserId());
        $other = $this->userDAL->getById($dto->getOtherId());

        if (!$user || !$other) {
            throw new EntityNotFoundException();
        }

        $achievements = $this->achievementDAL->findAll();
        $progressMap  = $this->buildProgressMap($user, $other);

        $stats = new ComparisonStats();

        $categories = $this->buildTree(
            $achievements,
            $progressMap,
            (string)$user->getId(),
            (string)$other->getId(),
            $stats
        );

        return $this->buildResponse($user, $other, $categories, $stats, count($achievements));
    }

    private function buildProgressMap(User $user, User $other): array
    {
        $progresses = $this->userAchievementProgressDAL->findByUsers($user, $other);

        $map = [];

        foreach ($progresses as $progress) {
            $map[(string)$progress->getAchievement()->getId()]
            [(string)$progress->getUser()->getId()] = $progress->getProgress();
        }

        return $map;
    }

    private function buildTree(
        array           $achievements,
        array           $progressMap,
        string          $userId,
        string          $otherId,
        ComparisonStats $stats
    ): array {

        $tree = [];

        foreach ($achievements as $achievement) {

            $group    = $achievement->getAchievementGroup();
            $category = $group?->getAchievementCategory();

            if (!$group || !$category) {
                continue;
            }

            $achievementId = (string)$achievement->getId();

            $userProgress  = $progressMap[$achievementId][$userId] ?? 0;
            $otherProgress = $progressMap[$achievementId][$otherId] ?? 0;

            $status = AchievementComparisonStatusEnum::fromProgress(
                $userProgress,
                $otherProgress
            );

            $stats->update($userProgress, $otherProgress, $status);

            $tree[$category->getPosition()]['groups'][$group->getPosition()]['achievements'][$achievement->getPosition()] = [
                'id'            => $achievement->getId(),
                'code'          => $achievement->getCode(),
                'position'      => $achievement->getPosition(),
                'userProgress'  => $userProgress,
                'otherProgress' => $otherProgress,
                'status'        => $status->value,
            ];

            $tree[$category->getPosition()] += [
                'id'       => $category->getId(),
                'code'     => $category->getCode(),
                'position' => $category->getPosition(),
            ];

            $tree[$category->getPosition()]['groups'][$group->getPosition()] += [
                'id'       => $group->getId(),
                'code'     => $group->getCode(),
                'position' => $group->getPosition(),
            ];
        }

        return $this->sortTree($tree);
    }

    private function sortTree(array $tree): array
    {
        ksort($tree);

        $sortedCategories = [];

        foreach ($tree as $category) {

            $groups = $category['groups'] ?? [];
            ksort($groups);

            $groupDTOs = [];

            foreach ($groups as $group) {

                $achievements = $group['achievements'] ?? [];
                ksort($achievements);

                $achievementDTOs = [];

                foreach ($achievements as $achievement) {
                    $achievementDTOs[] = new AchievementComparisonDTO(
                        $achievement['id'],
                        $achievement['code'],
                        $achievement['position'],
                        $achievement['userProgress'],
                        $achievement['otherProgress'],
                        $achievement['status'],
                    );
                }

                $groupDTOs[] = new GroupComparisonDTO(
                    $group['id'],
                    $group['code'],
                    $group['position'],
                    $achievementDTOs,
                );
            }

            $sortedCategories[] = new CategoryComparisonDTO(
                $category['id'],
                $category['code'],
                $category['position'],
                $groupDTOs,
            );
        }

        return $sortedCategories;
    }

    private function buildResponse(
        User            $user,
        User            $other,
        array           $categories,
        ComparisonStats $stats,
        int             $totalAchievements
    ): CompareUserAchievementsResponseDTO {

        $winnerGlobal = ComparisonWinnerEnum::fromScores(
            $stats->totalScoreUser,
            $stats->totalScoreOther,
        );

        $winnerDuel = ComparisonWinnerEnum::fromScores(
            $stats->duelScoreUser,
            $stats->duelScoreOther,
        );

        return new CompareUserAchievementsResponseDTO(
            new UserComparisonDTO(
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
            ),
            new UserComparisonDTO(
                $other->getId(),
                $other->getFirstName(),
                $other->getLastName(),
            ),
            new SummaryComparisonDTO(
                $totalAchievements,
                $stats->common,
                $stats->userOnly,
                $stats->otherOnly,
                round($stats->userUnlocked / max($totalAchievements, 1), 2),
                round($stats->otherUnlocked / max($totalAchievements, 1), 2),
                new ScoreComparisonDTO(
                    new ScoreDetailDTO(
                        $stats->totalScoreUser,
                        $stats->totalScoreOther,
                        $winnerGlobal->value,
                    ),
                    new ScoreDetailDTO(
                        $stats->duelScoreUser,
                        $stats->duelScoreOther,
                        $winnerDuel->value,
                    ),
                ),
            ),
            $categories,
        );
    }
}
