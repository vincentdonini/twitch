<?php

namespace App\UI\Controller\Exercise;

use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Domain\Exercise\ExerciseCategory\GetExerciseCategoryByIdUseCase;
use App\Domain\Exercise\ExerciseCategory\GetExerciseCategoryContentsByIdUseCase;
use App\Domain\Exercise\ExerciseCategory\ListExerciseCategoryUseCase;
use App\Domain\Exercise\ExerciseCategory\UpsertContentExerciseCategoryBulkUseCase;
use App\Domain\Exercise\ExerciseCategory\UpsertContentExerciseCategoryUseCase;
use App\Domain\Exercise\Filters\ExerciseCategoryFilterMapping;
use App\Domain\Exercise\Filters\ExerciseCategoryFilterRules;
use App\Domain\Exercise\Service\ExerciseCategoryService;
use App\Domain\Exercise\Sort\ExerciseCategorySortMapping;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Exercise\ExerciseCategory\GetExerciseCategoryByIdHttp;
use App\UI\Adapters\Http\Exercise\ExerciseCategory\ListExerciseCategoriesHttp;
use App\UI\Adapters\Http\Exercise\ExerciseCategory\UpsertContentExerciseCategoryBulkHttp;
use App\UI\Adapters\Http\Exercise\ExerciseCategory\UpsertContentExerciseCategoryHttp;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;
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
#[Route(path: '/exercise-categories', name: 'exercise_category_')]
final readonly class ExerciseCategoryController
{
    public function __construct(
        private ExerciseCategoryService $exerciseCategoryService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_EXERCISE_CATEGORY_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of exercise categories available in the system.',
        summary    : 'List of exercise categories.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // slug
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by Exercise category slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // name
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[name][eq]',
                description: 'Filter by Exercise category name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[name][like]',
                description: 'Filter by Exercise category name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // summary
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[summary][like]',
                description: 'Filter by Exercise category summary (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // details
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[details][like]',
                description: 'Filter by Exercise category details (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of exercise categories.',
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
                            type  : ExerciseCategory::class,
                            groups: [FrontGroupsEnum::EXERCISE_CATEGORY_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request                     $request,
        ListExerciseCategoryUseCase $useCase,
        NormalizerInterface         $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : ExerciseCategoryFilterMapping::FIELD_MAP,
            allowedOperators: ExerciseCategoryFilterRules::ALLOWED_OPERATORS,
            allowedFields   : ExerciseCategoryFilterRules::PUBLIC_FIELDS
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : ExerciseCategorySortMapping::FIELD_MAP,
            defaultSort: 'slug'
        );

        try {
            $paginator = $useCase->execute(
                new ListExerciseCategoriesHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->exerciseCategoryService->transformCollectionToDTO(
            exerciseCategories: $paginator->getItems(),
            filters           : $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::EXERCISE_CATEGORY_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{exerciseCategoryId}',
        name        : 'detail',
        requirements: [
            'exerciseCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_EXERCISE_CATEGORY_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific Exercise category.',
        summary    : 'Get Exercise category details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Exercise category details.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Exercise category not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetExerciseCategoryByIdUseCase $useCase,
        NormalizerInterface            $normalizer,
        string                         $exerciseCategoryId
    ): JsonResponse {
        try {
            $exerciseCategory = $useCase->execute(
                new GetExerciseCategoryByIdHttp(
                    id: Uuid::fromString($exerciseCategoryId)
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->exerciseCategoryService->transformToDTO($exerciseCategory);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::EXERCISE_CATEGORY_LIST,
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
        path        : '/{exerciseCategoryId}/contents',
        name        : 'contents_list',
        requirements: [
            'exerciseCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_EXERCISE_CATEGORY_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns all localized contents for an Exercise category.',
        summary    : 'List of all Exercise category contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of all Exercise category contents.',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : 'object',
                            groups: [FrontGroupsEnum::CONTENT_EXERCISE_CATEGORY_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function listContents(
        string                                 $exerciseCategoryId,
        NormalizerInterface                    $normalizer,
        GetExerciseCategoryContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $exerciseCategoryContents = $useCase->execute(
                new GetExerciseCategoryByIdHttp(
                    id: Uuid::fromString($exerciseCategoryId)
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $contentsArray = [];
        foreach ($exerciseCategoryContents as $content) {
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
                        FrontGroupsEnum::CONTENT_EXERCISE_CATEGORY_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{exerciseCategoryId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'exerciseCategoryId' => '[0-9a-fA-F\-]+',
            'locale'             => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_EXERCISE_CATEGORY_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update Exercise category content for a given locale.',
        summary    : 'Upsert Exercise category localized content.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['name', 'summary'],
                properties: [
                    new OAT\Property(
                        property: 'name',
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
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Exercise category not found.'),
        ]
    )]
    public function upsertContent(
        string                               $exerciseCategoryId,
        string                               $locale,
        Request                              $request,
        UpsertContentExerciseCategoryUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentExerciseCategoryHttp(
                    id     : Uuid::fromString($exerciseCategoryId),
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
        path        : '/{exerciseCategoryId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'exerciseCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_EXERCISE_CATEGORY_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an Exercise category.',
        summary    : 'Upsert multiple Exercise category contents.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "name"    => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        "details" => null,
                    ],
                    "en" => [
                        "name"    => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                        "details" => null,
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Exercise category not found.'),
        ]
    )]
    public function upsertContentsBulk(
        string                                   $exerciseCategoryId,
        Request                                  $request,
        UpsertContentExerciseCategoryBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentExerciseCategoryBulkHttp(
                    id     : Uuid::fromString($exerciseCategoryId),
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
