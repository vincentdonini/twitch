<?php

namespace App\Domain\Achievement\UserAchievementProgress;

use App\Domain\Achievement\Ports\UserAchievementProgressDALInterface;
use App\Domain\User\Entity\User;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use InvalidArgumentException;

final readonly class ListUserAchievementProgressesUseCase
{
    public function __construct(
        private UserAchievementProgressDALInterface $userAchievementProgressDAL,
    ) {
    }

    public function execute(
        ListUserAchievementProgressesDTOInterface $dto,
        ?User                                     $user,
    ): LightPaginator {
        if (!$this->validatePayload($dto)) {
            throw new InvalidArgumentException();
        }

        return $this->userAchievementProgressDAL->listUserAchievementProgresses(
            page : $dto->getPage(),
            limit: $dto->getLimit(),
            user : $user,
        );
    }

    private function validatePayload(ListUserAchievementProgressesDTOInterface $dto): bool
    {
        if ($dto->getPage() && $dto->getPage() < 0) {
            return false;
        }

        return true;
    }
}
