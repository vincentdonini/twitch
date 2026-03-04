<?php

namespace App\UI\Controller\Achievement;

use App\Domain\Achievement\AchievementCategory\CreateAchievementCategoryUseCase;
use App\Domain\Achievement\AchievementCategory\GetAchievementCategoryByIdUseCase;
use App\Domain\Achievement\AchievementCategory\GetAchievementCategoryContentsByIdUseCase;
use App\Domain\Achievement\AchievementCategory\ListAchievementCategoryUseCase;
use App\Domain\Achievement\AchievementCategory\UpdateAchievementCategoryUseCase;
use App\Domain\Achievement\AchievementCategory\UpsertContentAchievementCategoryBulkUseCase;
use App\Domain\Achievement\AchievementCategory\UpsertContentAchievementCategoryUseCase;
use App\Domain\Achievement\Entity\AchievementCategory;
use App\Domain\Achievement\Filters\AchievementCategoryFilterMapping;
use App\Domain\Achievement\Filters\AchievementCategoryFilterRules;
use App\Domain\Achievement\Service\AchievementCategoryService;
use App\Domain\Achievement\Sort\AchievementCategorySortMapping;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Achievement\AchievementCategory\CreateAchievementCategoryHttp;
use App\UI\Adapters\Http\Achievement\AchievementCategory\GetAchievementCategoryByIdHttp;
use App\UI\Adapters\Http\Achievement\AchievementCategory\ListAchievementCategoriesHttp;
use App\UI\Adapters\Http\Achievement\AchievementCategory\UpdateAchievementCategoryHttp;
use App\UI\Adapters\Http\Achievement\AchievementCategory\UpsertContentAchievementCategoryBulkHttp;
use App\UI\Adapters\Http\Achievement\AchievementCategory\UpsertContentAchievementCategoryHttp;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
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
#[Route(path: '/achievement-categories', name: 'achievement_category_')]
final class AchievementCategoryController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly AchievementCategoryService $achievementCategoryService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_CATEGORY_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of achievements category available in the system.',
        summary    : 'List of achievement categories',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // code
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[code][eq]',
                description: 'Filter by achievement category code (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of achievement categories',
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
                            type  : AchievementCategory::class,
                            groups: [FrontGroupsEnum::ACHIEVEMENT_CATEGORY_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request                        $request,
        ListAchievementCategoryUseCase $useCase,
        NormalizerInterface            $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : AchievementCategoryFilterMapping::FIELD_MAP,
            allowedOperators: AchievementCategoryFilterRules::ALLOWED_OPERATORS,
            allowedFields   : AchievementCategoryFilterRules::PUBLIC_FIELDS,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : AchievementCategorySortMapping::FIELD_MAP,
            defaultSort: 'position'
        );

        try {
            $paginator = $useCase->execute(
                new ListAchievementCategoriesHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->achievementCategoryService->transformCollectionToDTO(
            achievementCategories: $paginator->getItems(),
            filters              : $filters
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ACHIEVEMENT_CATEGORY_LIST,
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
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_CATEGORY_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Creates a new achievement category and returns the created resource ID.',
        summary    : 'Create a achievement category',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a achievement category',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : AchievementCategory::class,
                    groups: [FrontGroupsEnum::ACHIEVEMENT_CATEGORY_LIST]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Achievement category created successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of achievement category created',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : AchievementCategory::class,
                        groups: [FrontGroupsEnum::ACHIEVEMENT_CATEGORY_LIST]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request                          $request,
        CreateAchievementCategoryUseCase $useCase,
        NormalizerInterface              $normalizer,
    ): JsonResponse {
        try {
            $payload             = json_decode($request->getContent(), true);
            $achievementCategory = $useCase->execute(
                new CreateAchievementCategoryHttp($payload)
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $this->achievementCategoryService->transformToDTO($achievementCategory),
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::ACHIEVEMENT_CATEGORY_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $achievementCategory->getId()->toRfc4122()]
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{achievementCategoryId}',
        name        : 'detail',
        requirements: [
            'achievementCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_CATEGORY_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific achievement category.',
        summary    : 'Get achievement category details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of achievement group',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : AchievementCategory::class,
                        groups: [FrontGroupsEnum::ACHIEVEMENT_CATEGORY_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetAchievementCategoryByIdUseCase $useCase,
        NormalizerInterface               $normalizer,
        string                            $achievementCategoryId
    ): JsonResponse {
        try {
            $achievementCategory = $useCase->execute(
                new GetAchievementCategoryByIdHttp(
                    id: Uuid::fromString($achievementCategoryId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->achievementCategoryService->transformToDTO($achievementCategory);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ACHIEVEMENT_CATEGORY_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/{achievementCategoryId}',
        name   : 'update',
        methods: ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ACHIEVEMENT_CATEGORY_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Patch(
        description: 'Updates an existing achievement category with the provided data.',
        summary    : 'Update a achievement',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Update a achievement category',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : AchievementCategory::class,
                    groups: [FrontGroupsEnum::ACHIEVEMENT_CATEGORY_LIST]
                ),
            ),
        ),
        parameters : [
            new OAT\PathParameter(
                name       : 'code',
                description: 'Code of the achievement category',
                required   : true,
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\PathParameter(
                name       : 'position',
                description: 'Position of the achievement category',
                required   : true,
                schema     : new OAT\Schema(type: 'integer'),
            ),
            new OAT\PathParameter(
                name       : 'achievementCategoryId',
                description: 'Achievement category ID of the achievement',
                required   : true,
                schema     : new OAT\Schema(type: 'integer'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_NO_CONTENT,
                description: 'Achievement category updated successfully',
            ),
        ]
    )]
    public function patch(
        Request                          $request,
        string                           $achievementCategoryId,
        UpdateAchievementCategoryUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdateAchievementCategoryHttp(
                    id     : Uuid::fromString($achievementCategoryId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------

    #[Route(
        path        : '/{achievementCategoryId}/contents',
        name        : 'contents_list',
        requirements: [
            'achievementCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_CATEGORY_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an achievement category',
        summary    : 'Get achievement category contents',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Achievement category not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                                    $achievementCategoryId,
        NormalizerInterface                       $normalizer,
        GetAchievementCategoryContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $achievementContents = $useCase->execute(
                new GetAchievementCategoryByIdHttp(
                    id: Uuid::fromString($achievementCategoryId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $contentsArray = [];
        foreach ($achievementContents as $content) {
            $contentsArray[$content->getLocale()] = [
                'title'       => $content->getTitle(),
                'description' => $content->getDescription(),
            ];
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $contentsArray,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::CONTENT_ACHIEVEMENT_CATEGORY_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{achievementCategoryId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'achievementCategoryId' => '[0-9a-fA-F\-]+',
            'locale'                => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_CATEGORY_MANAGE)]
    #[OAT\Put(
        description: 'Create or update achievement category content for a given locale.',
        summary    : 'Upsert achievement category localized content.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['title', 'description'],
                properties: [
                    new OAT\Property(
                        property: 'title',
                        type    : 'string',
                        example : 'Lorem ipsum'
                    ),
                    new OAT\Property(
                        property: 'description',
                        type    : 'string',
                        example : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.'
                    ),
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Content updated.'),
            new OAT\Response(response: 201, description: 'Content created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Achievement category not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContent(
        string                                  $achievementCategoryId,
        string                                  $locale,
        Request                                 $request,
        UpsertContentAchievementCategoryUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentAchievementCategoryHttp(
                    id     : Uuid::fromString($achievementCategoryId),
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
        path        : '/{achievementCategoryId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: ['wodId' => '\d+'],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_ACHIEVEMENT_CATEGORY_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an Achievement category.',
        summary    : 'Upsert multiple Achievement category contents.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "title"       => "Lorem ipsum",
                        "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                    ],
                    "en" => [
                        "title"       => "Lorem ipsum",
                        "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'Achievement category not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                                      $achievementCategoryId,
        Request                                     $request,
        UpsertContentAchievementCategoryBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentAchievementCategoryBulkHttp(
                    id     : Uuid::fromString($achievementCategoryId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
