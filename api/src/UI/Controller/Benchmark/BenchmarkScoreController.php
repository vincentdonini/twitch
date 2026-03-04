<?php

namespace App\UI\Controller\Benchmark;

use App\Domain\Benchmark\Sort\BenchmarkScoreSortMapping;
use App\Domain\User\Entity\User;
use App\Domain\Benchmark\Entity\BenchmarkScore;
use App\Domain\Benchmark\Filters\BenchmarkScoreFilterMapping;
use App\Domain\Benchmark\Filters\BenchmarkScoreFilterRules;
use App\Domain\Benchmark\Service\BenchmarkScoreService;
use App\Domain\Benchmark\BenchmarkScore\CreateBenchmarkScoreUseCase;
use App\Domain\Benchmark\BenchmarkScore\GetBenchmarkScoreByIdUseCase;
use App\Domain\Benchmark\BenchmarkScore\ListBenchmarkScoresUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Benchmark\BenchmarkScore\CreateBenchmarkScoreHttp;
use App\UI\Adapters\Http\Benchmark\BenchmarkScore\GetBenchmarkScoreByIdHttp;
use App\UI\Adapters\Http\Benchmark\BenchmarkScore\ListBenchmarkScoresHttp;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Attribute\Model;
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
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route('/benchmark-scores', name: 'benchmark_score_')]
final class BenchmarkScoreController extends AbstractController
{
    public function __construct(
        private readonly BenchmarkScoreService $benchmarkScoreService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_SCORE_LIST)]
    #[OAT\Get(
        description: 'Returns a list of all benchmark scores available in the system.',
        summary    : 'Retrieve all benchmark scores',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // user
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[user.id][eq]',
                description: 'Filter by user ID (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.firstName][eq]',
                description: 'Filter by user firstName (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.firstName][like]',
                description: 'Filter by user firstName (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.lastName][eq]',
                description: 'Filter by user lastName (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[user.lastName][like]',
                description: 'Filter by user firstName (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // benchmark
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[benchmark.id][eq]',
                description: 'Filter by benchmark ID (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[benchmark.slug][eq]',
                description: 'Filter by benchmark slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[benchmark.name][eq]',
                description: 'Filter by benchmark name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[benchmark.name][like]',
                description: 'Filter by benchmark name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of benchmark scores'),
            new OAT\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function list(
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
            defaultSort: 'performedAt'
        );

        $paginator = $useCase->execute(
            new ListBenchmarkScoresHttp(
                page   : $paginatorValues->getPage(),
                limit  : $paginatorValues->getLimit(),
                filters: $filters,
                sorts  : $sorts,
            ),
            user: null,
        );

        $dtoItems = $this->benchmarkScoreService->transformCollectionToDTO(
            benchmarkScores: $paginator->getItems(),
            filters        : $filters,
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::BENCHMARK_SCORE_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(
                paginator      : $paginator,
                paginatorValues: $paginatorValues
            )
        );
    }

    #[Route(
        path   : '',
        name   : 'create',
        methods: ['POST']
    )]
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_SCORE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Creates a new benchmark score and returns the created resource ID.',
        summary    : 'Create a benchmark score',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a benchmark score',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : BenchmarkScore::class,
                    groups: [FrontGroupsEnum::BENCHMARK_SCORE_MANAGE]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Benchmark score created successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of benchmark score created',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : BenchmarkScore::class,
                        groups: [FrontGroupsEnum::BENCHMARK_SCORE_MANAGE]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request                     $request,
        CreateBenchmarkScoreUseCase $useCase,
        NormalizerInterface         $normalizer,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new InvalidArgumentException();
        }

        try {
            $payload        = json_decode($request->getContent(), true);
            $benchmarkScore = $useCase->execute(
                new CreateBenchmarkScoreHttp(
                    payload: $payload,
                    userId : $user->getId()
                )
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $benchmarkScore,
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::BENCHMARK_SCORE_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $benchmarkScore->getId()]
            );
        } catch (InvalidArgumentException) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new JsonResponse(null, $statusCode);
    }

    #[Route(
        path        : '/{benchmarkScoreId}',
        name        : 'detail',
        requirements: [
            'benchmarkScoreId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_SCORE_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific benchmark score.',
        summary    : 'Get benchmark score details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Benchmark score details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Benchmark score not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetBenchmarkScoreByIdUseCase $useCase,
        NormalizerInterface          $normalizer,
        string                       $benchmarkScoreId
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_FORBIDDEN
            );
        }

        try {
            $benchmarkScore = $useCase->execute(
                new GetBenchmarkScoreByIdHttp(
                    id: Uuid::fromString($benchmarkScoreId),
                )
            );

            if (
                $benchmarkScore->getUser()->getId() !== $user->getId() &&
                !in_array('ROLE_ADMIN', $user->getRoles(), true)
            ) {
                return new JsonResponse(
                    data  : null,
                    status: Response::HTTP_FORBIDDEN
                );
            }
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->benchmarkScoreService->transformToDTO($benchmarkScore);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::BENCHMARK_SCORE_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }
}
