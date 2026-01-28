<?php

namespace App\UI\Controller\User;

use App\Domain\Benchmark\BenchmarkScore\ListBenchmarkScoresUseCase;
use App\Domain\Benchmark\Filters\BenchmarkScoreFilterMapping;
use App\Domain\Benchmark\Filters\BenchmarkScoreFilterRules;
use App\Domain\Benchmark\Service\BenchmarkScoreService;
use App\Domain\Benchmark\Sort\BenchmarkScoreSortMapping;
use App\Domain\User\Ports\UserDALInterface;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Benchmark\BenchmarkScore\ListBenchmarkScoresHttp;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
#[Route(path: '/users', name: 'user_benchmark_scores_')]
final class UserBenchmarkScoreController extends AbstractController
{
    public function __construct(
        private readonly BenchmarkScoreService $benchmarkScoreService,
        private readonly UserDALInterface      $userDAL,
    ) {
    }

    #[Route(
        path        : '/{userId}/benchmark-scores',
        name        : 'detail',
        requirements: [
            'userId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_SCORE_LIST)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific user.',
        summary    : 'Get user details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'User details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'User not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        Request                    $request,
        ListBenchmarkScoresUseCase $useCase,
        NormalizerInterface        $normalizer,
        string                     $userId
    ): JsonResponse {
        $user = $this->userDAL->getById($userId);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : BenchmarkScoreFilterMapping::FIELD_MAP,
            allowedOperators: BenchmarkScoreFilterRules::ALLOWED_OPERATORS,
            allowedFields   : BenchmarkScoreFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : BenchmarkScoreSortMapping::FIELD_MAP,
            defaultSort: 'name'
        );

        try {
            $paginator = $useCase->execute(
                new ListBenchmarkScoresHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                ),
                user: $user
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->benchmarkScoreService->transformCollectionToDTO(
            benchmarkScores: $paginator->getItems(),
            filters        : $filters
        );

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::USER_ME,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/me/benchmark-scores',
        name   : 'me_benchmark_scores',
        methods: ['GET']
    )]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[OAT\Get(
        description: 'Returns the benchmark scores of the currently authenticated user.',
        summary    : 'Get benchmark scores for current user',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'User profile'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'User not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function me(
        Request                    $request,
        ListBenchmarkScoresUseCase $useCase,
        NormalizerInterface        $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : BenchmarkScoreFilterMapping::FIELD_MAP,
            allowedOperators: BenchmarkScoreFilterRules::ALLOWED_OPERATORS,
            allowedFields   : BenchmarkScoreFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : BenchmarkScoreSortMapping::FIELD_MAP,
            defaultSort: 'name'
        );

        try {
            $paginator = $useCase->execute(
                new ListBenchmarkScoresHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                ),
                user: $this->getUser()
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->benchmarkScoreService->transformCollectionToDTO(
            benchmarkScores: $paginator->getItems(),
            filters        : $filters
        );

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::USER_ME,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }
}
