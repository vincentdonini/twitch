<?php

namespace App\UI\Adapters\Http\Wod\WodScore;

use App\Domain\Wod\Wod\GetWodLeaderboardDTOInterface;
use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Sorts\SortCollection;
use Symfony\Component\Uid\Uuid;

final readonly class GetWodLeaderboardHttp implements GetWodLeaderboardDTOInterface
{
    public function __construct(
        private Uuid              $wodId,
        private ?Uuid             $wodDivisionId = null,
        private ?string           $gender = null,
        private ?int              $page = null,
        private ?int              $limit = null,
        private ?FilterCollection $filters = null,
        private ?SortCollection   $sorts = null,
    ) {
    }

    public function getWodId(): Uuid
    {
        return $this->wodId;
    }

    public function getWodDivisionId(): ?Uuid
    {
        return $this->wodDivisionId;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function getLimit(): int
    {
        return $this->limit ?? RequestPaginator::DEFAULT_LIMIT;
    }

    public function getFilters(): ?FilterCollection
    {
        return $this->filters;
    }

    public function getSorts(): ?SortCollection
    {
        return $this->sorts;
    }
}
