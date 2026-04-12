<?php

namespace App\Domain\Muscle\MuscleSegment;

use App\Domain\Muscle\Ports\MuscleSegmentDALInterface;
use App\Infrastructure\Doctrine\Pagination\LightPaginator;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;

final readonly class ListMuscleSegmentsUseCase
{
    public function __construct(
        private MuscleSegmentDALInterface $muscleSegmentDAL,
    ) {
    }

    public function execute(
        int               $page = 1,
        int               $limit = RequestPaginator::DEFAULT_LIMIT,
        ?FilterCollection $filters = null,
        ?SortCollection   $sorts = null,
    ): LightPaginator {
        if ($page < 0) {
            $page = 1;
        }

        return $this->muscleSegmentDAL->listMuscleSegments($page, $limit, $filters, $sorts);
    }
}
