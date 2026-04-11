<?php

namespace App\UI\Controller\Muscle;

use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Filters\MuscleFilterMapping;
use App\Domain\Muscle\Filters\MuscleFilterRules;
use App\Domain\Muscle\Muscle\GetMuscleByIdUseCase;
use App\Domain\Muscle\Muscle\GetMuscleContentsByIdUseCase;
use App\Domain\Muscle\Muscle\ListMusclesUseCase;
use App\Domain\Muscle\Muscle\UpsertContentMuscleBulkUseCase;
use App\Domain\Muscle\Muscle\UpsertContentMuscleUseCase;
use App\Domain\Muscle\Ports\MuscleDALInterface;
use App\Domain\Muscle\Service\MuscleService;
use App\Domain\Muscle\Sort\MuscleSortMapping;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\Muscle\Muscle\GetMuscleByIdHttp;
use App\UI\Adapters\Http\Muscle\Muscle\ListMusclesHttp;
use App\UI\Adapters\Http\Muscle\Muscle\UpsertContentMuscleBulkHttp;
use App\UI\Adapters\Http\Muscle\Muscle\UpsertContentMuscleHttp;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route(path: '/muscles', name: 'muscle_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final readonly class MuscleController
{
    use ApiExceptionHandler;

    public function __construct(
        private MuscleService      $muscleService,
        private MuscleDALInterface $muscleDAL,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of Muscles available in the system.',
        summary    : 'List of Muscles.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // slug
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by Muscle slug (exact match)',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // title
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[title][eq]',
                description: 'Filter by Muscle title (exact match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'filters[title][like]',
                description: 'Filter by Muscle title (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // summary
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[summary][like]',
                description: 'Filter by Muscle summary (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // details
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[details][like]',
                description: 'Filter by Muscle details (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of muscles.',
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
                            type  : Muscle::class,
                            groups: [FrontGroupsEnum::MUSCLE_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request             $request,
        ListMusclesUseCase  $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : MuscleFilterMapping::FIELD_MAP,
            allowedOperators: MuscleFilterRules::ALLOWED_OPERATORS,
            allowedFields   : MuscleFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : MuscleSortMapping::FIELD_MAP,
            defaultSort: 'slug'
        );

        try {
            $paginator = $useCase->execute(
                new ListMusclesHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->muscleService->transformCollectionToDTO(
            muscles   : $paginator->getItems(),
            filters   : $filters,
            wodCounts : $this->muscleDAL->getWodCounts(),
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{muscleId}',
        name        : 'detail',
        requirements: [
            'muscleId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific Muscle.',
        summary    : 'Get Muscle details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Muscle details.',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Muscle::class,
                        groups: [FrontGroupsEnum::MUSCLE_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetMuscleByIdUseCase $useCase,
        NormalizerInterface  $normalizer,
        string               $muscleId
    ): JsonResponse {
        try {
            $muscle = $useCase->execute(
                new GetMuscleByIdHttp(
                    id: Uuid::fromString($muscleId)
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->muscleService->transformToDTO($muscle);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::MUSCLE_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/{muscleId}/contents',
        name        : 'contents_list',
        requirements: [
            'muscleId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns all localized contents for an Muscle.',
        summary    : 'List of all Muscle contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of all Muscle contents.',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : 'object',
                            groups: [FrontGroupsEnum::CONTENT_MUSCLE_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function listContents(
        string                       $muscleId,
        NormalizerInterface          $normalizer,
        GetMuscleContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $muscleContents = $useCase->execute(
                new GetMuscleByIdHttp(
                    id: Uuid::fromString($muscleId)
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $contentsArray = [];
        foreach ($muscleContents as $content) {
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
                        FrontGroupsEnum::CONTENT_MUSCLE_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{muscleId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'muscleId' => '[0-9a-fA-F\-]+',
            'locale'   => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update Muscle content for a given locale.',
        summary    : 'Upsert Muscle localized content.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['title', 'summary'],
                properties: [
                    new OAT\Property(
                        property: 'title',
                        type    : 'string',
                        example : 'Lorem ipsum'
                    ),
                    new OAT\Property(
                        property: 'summary',
                        type    : 'string',
                        example : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.'
                    ),
                    new OAT\Property(
                        property: 'details',
                        type    : 'string',
                        nullable: true
                    ),
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: Response::HTTP_NO_CONTENT, description: 'Content saved.'),
            new OAT\Response(response: Response::HTTP_BAD_REQUEST, description: 'Invalid payload.'),
            new OAT\Response(response: Response::HTTP_NOT_FOUND, description: 'Muscle not found.'),
        ]
    )]
    public function upsertContent(
        string                     $muscleId,
        string                     $locale,
        Request                    $request,
        UpsertContentMuscleUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleHttp(
                    id     : Uuid::fromString($muscleId),
                    locale : $locale,
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{muscleId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'muscleId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an Muscle.',
        summary    : 'Upsert multiple Muscle contents.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "title"   => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        "details" => null,
                    ],
                    "en" => [
                        "title"   => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                        "details" => null,
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: Response::HTTP_NO_CONTENT, description: 'Contents saved.'),
            new OAT\Response(response: Response::HTTP_BAD_REQUEST, description: 'Invalid payload.'),
            new OAT\Response(response: Response::HTTP_NOT_FOUND, description: 'Muscle not found.'),
        ]
    )]
    public function upsertContentsBulk(
        string                         $muscleId,
        Request                        $request,
        UpsertContentMuscleBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentMuscleBulkHttp(
                    id     : Uuid::fromString($muscleId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
