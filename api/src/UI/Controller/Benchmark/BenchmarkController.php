<?php

namespace App\UI\Controller\Benchmark;

use App\Domain\Benchmark\Benchmark\GetBenchmarkContentsByIdUseCase;
use App\Domain\Benchmark\Benchmark\UpsertContentBenchmarkBulkUseCase;
use App\Domain\Benchmark\Benchmark\UpsertContentBenchmarkUseCase;
use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Benchmark\Entity\Benchmark;
use App\Domain\Benchmark\Filters\BenchmarkFilterMapping;
use App\Domain\Benchmark\Filters\BenchmarkFilterRules;
use App\Domain\Benchmark\Service\BenchmarkService;
use App\Domain\Benchmark\Sort\BenchmarkSortMapping;
use App\Domain\Benchmark\Benchmark\CreateBenchmarkUseCase;
use App\Domain\Benchmark\Benchmark\GetBenchmarkByIdUseCase;
use App\Domain\Benchmark\Benchmark\ListBenchmarkUseCase;
use App\Domain\Benchmark\Benchmark\UpdateBenchmarkUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Benchmark\Benchmark\CreateBenchmarkHttp;
use App\UI\Adapters\Http\Benchmark\Benchmark\GetBenchmarkByIdHttp;
use App\UI\Adapters\Http\Benchmark\Benchmark\ListBenchmarksHttp;
use App\UI\Adapters\Http\Benchmark\Benchmark\UpdateBenchmarkHttp;
use App\UI\Adapters\Http\Benchmark\Benchmark\UpsertContentBenchmarkBulkHttp;
use App\UI\Adapters\Http\Benchmark\Benchmark\UpsertContentBenchmarkHttp;
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
#[Route(path: '/benchmarks', name: 'benchmark_')]
final class BenchmarkController extends AbstractController
{
    public function __construct(
        private readonly BenchmarkService $benchmarkService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of benchmarks available in the system.',
        summary    : 'List of benchmarks',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // slug
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by benchmark slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // name
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[name][eq]',
                description: 'Filter by benchmark name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[name][like]',
                description: 'Filter by benchmark name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // type
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[type][eq]',
                description: 'Filter by benchmark type (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // exercise.id (exercise.id)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[exercise.id][eq]',
                description: 'Filter by exercise ID',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // exercise.slug (exercise.slug)
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[exercise.slug][eq]',
                description: 'Filter by exercise slug',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of benchmarks',
                headers    : [
                    new OAT\Header(ref: '#/components/headers/Element-Count', header: 'Element-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Page', header: 'Pagination-Page'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Count', header: 'Pagination-Count'),
                    new OAT\Header(ref: '#/components/headers/Pagination-Limit', header: 'Pagination-Limit'),
                ],
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : Benchmark::class,
                            groups: [FrontGroupsEnum::BENCHMARK_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request              $request,
        ListBenchmarkUseCase $useCase,
        NormalizerInterface  $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : BenchmarkFilterMapping::FIELD_MAP,
            allowedOperators: BenchmarkFilterRules::ALLOWED_OPERATORS,
            allowedFields   : BenchmarkFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : BenchmarkSortMapping::FIELD_MAP,
            defaultSort: 'name'
        );

        try {
            $paginator = $useCase->execute(
                new ListBenchmarksHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->benchmarkService->transformCollectionToDTO(
            benchmarks: $paginator->getItems(),
            filters   : $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::BENCHMARK_LIST,
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
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Creates a new benchmark and returns the created resource ID.',
        summary    : 'Create a benchmark',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a benchmark',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Benchmark::class,
                    groups: [FrontGroupsEnum::BENCHMARK_LIST]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Benchmark created successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of benchmark created',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : Benchmark::class,
                        groups: [FrontGroupsEnum::BENCHMARK_LIST]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request                $request,
        CreateBenchmarkUseCase $useCase,
        NormalizerInterface    $normalizer,
    ): JsonResponse {
        try {
            $payload   = json_decode($request->getContent(), true);
            $benchmark = $useCase->execute(
                new CreateBenchmarkHttp($payload)
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $benchmark,
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::BENCHMARK_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $benchmark->getId()]
            );
        } catch (InvalidArgumentException) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        } catch (AlreadyExistException) {
            $statusCode = Response::HTTP_CONFLICT;
        }

        return new JsonResponse(null, $statusCode);
    }

    #[Route(
        path        : '/{benchmarkId}',
        name        : 'detail',
        requirements: [
            'benchmarkId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific benchmark.',
        summary    : 'Get benchmark details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of benchmark',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Benchmark::class,
                        groups: [FrontGroupsEnum::BENCHMARK_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetBenchmarkByIdUseCase $useCase,
        NormalizerInterface     $normalizer,
        string                  $benchmarkId
    ): JsonResponse {
        try {
            $benchmark = $useCase->execute(
                new GetBenchmarkByIdHttp(
                    id: Uuid::fromString($benchmarkId),
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->benchmarkService->transformToDTO($benchmark);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::BENCHMARK_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/{benchmarkId}',
        name   : 'update',
        methods: ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_BENCHMARK_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Patch(
        description: 'Updates an existing benchmark with the provided data.',
        summary    : 'Update a benchmark',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Update a benchmark',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Benchmark::class,
                    groups: [FrontGroupsEnum::BENCHMARK_LIST]
                ),
            ),
        ),
        parameters : [
            new OAT\PathParameter(
                name       : 'name',
                description: 'Name of the benchmark',
                required   : true,
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\PathParameter(
                name       : 'type',
                description: 'Type of the benchmark',
                required   : true,
                schema     : new OAT\Schema(type: 'integer'),
            ),
            new OAT\PathParameter(
                name       : 'exerciseId',
                description: 'Exercise ID of the benchmark',
                required   : true,
                schema     : new OAT\Schema(type: 'integer'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_NO_CONTENT,
                description: 'Benchmark updated successfully',
            ),
        ]
    )]
    public function patch(
        Request                $request,
        string                 $benchmarkId,
        UpdateBenchmarkUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdateBenchmarkHttp(
                    id     : $benchmarkId,
                    payload: $payload
                )
            );

            $statusCode = Response::HTTP_NO_CONTENT;
        } catch (EntityNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse(null, $statusCode);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/{benchmarkId}/contents',
        name        : 'contents_list',
        requirements: ['benchmarkId' => '\d+'],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_BENCHMARK_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an benchmark',
        summary    : 'Get benchmark contents',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Benchmark not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                          $benchmarkId,
        NormalizerInterface             $normalizer,
        GetBenchmarkContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $wodCategoryContents = $useCase->execute(
                new GetBenchmarkByIdHttp($benchmarkId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $contentsArray = [];
        foreach ($wodCategoryContents as $content) {
            $contentsArray[$content->getLocale()] = [
                'title'   => $content->getTitle(),
                'summary' => $content->getSummary(),
                'details' => $content->getDetails(),
            ];
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $contentsArray,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::CONTENT_BENCHMARK_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{benchmarkId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'benchmarkId' => '\d+',
            'locale'      => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_BENCHMARK_MANAGE)]
    #[OAT\Put(
        description: 'Create or update benchmark content for a given locale.',
        summary    : 'Upsert exercise localized content',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['name', 'summary'],
                properties: [
                    new OAT\Property(property: 'name', type: 'string', example: 'Barbell'),
                    new OAT\Property(property: 'summary', type: 'string', example: 'Barre utilisée pour les exercices de force'),
                    new OAT\Property(property: 'details', type: 'string', nullable: true),
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Content updated'),
            new OAT\Response(response: 201, description: 'Content created'),
            new OAT\Response(response: 400, description: 'Invalid payload'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Exercise not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContent(
        string                        $benchmarkId,
        string                        $locale,
        Request                       $request,
        UpsertContentBenchmarkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentBenchmarkHttp(
                    id     : $benchmarkId,
                    locale : $locale,
                    payload: $payload
                )
            );

            $statusCode = Response::HTTP_NO_CONTENT;
        } catch (EntityNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse(null, $statusCode);
    }

    #[Route(
        path        : '/{benchmarkId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: ['wodId' => '\d+'],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_BENCHMARK_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an benchmark',
        summary    : 'Upsert multiple benchmark contents',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "name"    => "Barbell",
                        "summary" => "Barre utilisée pour les exercices de force",
                        "details" => "Squats, deadlifts...",
                    ],
                    "en" => [
                        "name"    => "Barbell",
                        "summary" => "Traditional bar used for strength exercises",
                        "details" => "Squats, deadlifts...",
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Contents updated'),
            new OAT\Response(response: 201, description: 'Contents created'),
            new OAT\Response(response: 400, description: 'Invalid payload'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'benchmark not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                            $benchmarkId,
        Request                           $request,
        UpsertContentBenchmarkBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentBenchmarkBulkHttp(
                    id     : $benchmarkId,
                    payload: $payload
                )
            );

            $statusCode = Response::HTTP_NO_CONTENT;
        } catch (EntityNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse(null, $statusCode);
    }
}
