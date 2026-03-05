<?php

namespace App\UI\Controller\Wod;

use App\Domain\Wod\Service\WodCategoryService;
use App\Domain\Wod\WodCategory\GetWodCategoryByIdUseCase;
use App\Domain\Wod\WodCategory\GetWodCategoryContentsByIdUseCase;
use App\Domain\Wod\WodCategory\ListWodCategoriesUseCase;
use App\Domain\Wod\WodCategory\UpsertContentWodCategoryBulkUseCase;
use App\Domain\Wod\WodCategory\UpsertContentWodCategoryUseCase;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Wod\WodCategory\GetWodCategoryByIdHttp;
use App\UI\Adapters\Http\Wod\WodCategory\ListWodCategoriesHttp;
use App\UI\Adapters\Http\Wod\WodCategory\UpsertContentWodCategoryBulkHttp;
use App\UI\Adapters\Http\Wod\WodCategory\UpsertContentWodCategoryHttp;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
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
#[Route(path: '/wod-categories', name: 'wod_category_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final readonly class WodCategoryController
{
    use ApiExceptionHandler;

    public function __construct(
        private WodCategoryService $wodCategoryService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_CATEGORY_LIST)]
    #[OAT\Get(
        description: 'Returns a list of all WOD Categories available in the system.',
        summary    : 'List of WOD Categories',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of WOD Categories.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function list(
        Request                  $request,
        ListWodCategoriesUseCase $useCase,
        NormalizerInterface      $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues($request);

        try {
            $paginator = $useCase->execute(
                new ListWodCategoriesHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->wodCategoryService->transformCollectionToDTO(
            wodCategories: $paginator->getItems(),
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_CATEGORY_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{wodCategoryId}',
        name        : 'detail',
        requirements: [
            'wodCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_CATEGORY_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific WOD Category.',
        summary    : 'Get WOD Category details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'WOD Category details.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Category not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetWodCategoryByIdUseCase $useCase,
        NormalizerInterface       $normalizer,
        string                    $wodCategoryId
    ): JsonResponse {
        try {
            $wodCategory = $useCase->execute(
                new GetWodCategoryByIdHttp(
                    id: Uuid::fromString($wodCategoryId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->wodCategoryService->transformToDTO($wodCategory);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_CATEGORY_DETAIL,
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
        path        : '/{wodCategoryId}/contents',
        name        : 'contents_list',
        requirements: [
            'wodCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_CATEGORY_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an WOD Category.',
        summary    : 'Get WOD Category contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Category not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                            $wodCategoryId,
        NormalizerInterface               $normalizer,
        GetWodCategoryContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $wodCategoryContents = $useCase->execute(
                new GetWodCategoryByIdHttp(
                    id: Uuid::fromString($wodCategoryId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
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
                        FrontGroupsEnum::CONTENT_WOD_CATEGORY_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{wodCategoryId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'wodCategoryId' => '[0-9a-fA-F\-]+',
            'locale'        => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_CATEGORY_MANAGE)]
    #[OAT\Put(
        description: 'Create or update WOD Category content for a given locale.',
        summary    : 'Upsert WOD Category localized content.',
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
            new OAT\Response(response: Response::HTTP_NOT_FOUND, description: 'WOD Category not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContent(
        string                          $wodCategoryId,
        string                          $locale,
        Request                         $request,
        UpsertContentWodCategoryUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodCategoryHttp(
                    id     : Uuid::fromString($wodCategoryId),
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
        path        : '/{wodCategoryId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'wodCategoryId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_CATEGORY_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an WOD Category.',
        summary    : 'Upsert multiple WOD Category contents.',
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
            new OAT\Response(response: Response::HTTP_NOT_FOUND, description: 'WOD Category not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                              $wodCategoryId,
        Request                             $request,
        UpsertContentWodCategoryBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodCategoryBulkHttp(
                    id     : Uuid::fromString($wodCategoryId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
