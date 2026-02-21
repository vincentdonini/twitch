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
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Attribute\Security;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
#[Route(path: '/wod-categories', name: 'wod_category_')]
final readonly class WodCategoryController
{
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
        description: 'Returns a list of all WOD categories available in the system.',
        summary    : 'Retrieve all WOD categories',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of WOD categories'),
            new OAT\Response(response: 403, description: 'Access denied'),
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
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
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
            'wodCategoryId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_CATEGORY_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific WOD category.',
        summary    : 'Get WOD category details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'WOD category details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'WOD category not found'),
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
                new GetWodCategoryByIdHttp($wodCategoryId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->wodCategoryService->transformToDTO($wodCategory);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_CATEGORY_LIST,
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
            'wodCategoryId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_CATEGORY_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an WOD category',
        summary    : 'Get WOD category contents',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'WOD category not found'),
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
                new GetWodCategoryByIdHttp($wodCategoryId)
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
            'wodCategoryId' => '\d+',
            'locale'        => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_CATEGORY_MANAGE)]
    #[OAT\Put(
        description: 'Create or update exercise content for a given locale.',
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
        string                          $wodCategoryId,
        string                          $locale,
        Request                         $request,
        UpsertContentWodCategoryUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodCategoryHttp(
                    id     : $wodCategoryId,
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
        path        : '/{wodCategoryId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'wodId' => '\d+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_CATEGORY_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an WOD category',
        summary    : 'Upsert multiple WOD category contents',
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
            new OAT\Response(response: 404, description: 'WOD category not found'),
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
                    id     : $wodCategoryId,
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
