<?php

namespace App\UI\Controller\Wod;

use App\Domain\Wod\Service\WodTypeService;
use App\Domain\Wod\WodType\GetWodTypeByIdUseCase;
use App\Domain\Wod\WodType\GetWodTypeContentsByIdUseCase;
use App\Domain\Wod\WodType\ListWodTypesUseCase;
use App\Domain\Wod\WodType\UpsertContentWodTypeBulkUseCase;
use App\Domain\Wod\WodType\UpsertContentWodTypeUseCase;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Wod\WodType\GetWodTypeByIdHttp;
use App\UI\Adapters\Http\Wod\WodType\ListWodTypesHttp;
use App\UI\Adapters\Http\Wod\WodType\UpsertContentWodTypeBulkHttp;
use App\UI\Adapters\Http\Wod\WodType\UpsertContentWodTypeHttp;
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
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route(path: '/wod-types', name: 'wod_type_')]
final readonly class WodTypeController
{
    public function __construct(
        private WodTypeService $wodTypeService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_TYPE_LIST)]
    #[OAT\Get(
        description: 'Returns a list of all WOD Types available in the system.',
        summary    : 'List of WOD Types.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // slug
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by WOD Type slug (exact match).',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // title
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[title][eq]',
                description: 'Filter by WOD Type title (exact match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'filters[title][like]',
                description: 'Filter by WOD Type title (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // summary
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[summary][like]',
                description: 'Filter by WOD Type summary (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),

            // ---------------------------------------------------------------------------------------------------------
            // details
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[details][like]',
                description: 'Filter by WOD Type details (partial match).',
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of WOD Types.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function list(
        Request             $request,
        ListWodTypesUseCase $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues($request);

        try {
            $paginator = $useCase->execute(
                new ListWodTypesHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->wodTypeService->transformCollectionToDTO($paginator->getItems());

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_TYPE_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{wodTypeId}',
        name        : 'detail',
        requirements: [
            'wodTypeId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_TYPE_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific WOD Type.',
        summary    : 'Get WOD Type details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'WOD Type details.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Type not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetWodTypeByIdUseCase $useCase,
        NormalizerInterface   $normalizer,
        string                $wodTypeId
    ): JsonResponse {
        try {
            $wodType = $useCase->execute(
                new GetWodTypeByIdHttp(
                    id: Uuid::fromString($wodTypeId),
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->wodTypeService->transformToDTO($wodType);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_TYPE_LIST,
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
        path        : '/{wodTypeId}/contents',
        name        : 'contents_list',
        requirements: [
            'wodTypeId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_TYPE_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an WOD Type.',
        summary    : 'Get WOD Type contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Type not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                        $wodTypeId,
        NormalizerInterface           $normalizer,
        GetWodTypeContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $wodTypeContents = $useCase->execute(
                new GetWodTypeByIdHttp(
                    id: Uuid::fromString($wodTypeId),
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $contentsArray = [];
        foreach ($wodTypeContents as $content) {
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
                        FrontGroupsEnum::CONTENT_WOD_TYPE_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{wodTypeId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'wodTypeId' => '[0-9a-fA-F\-]+',
            'locale'    => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_TYPE_MANAGE)]
    #[OAT\Put(
        description: 'Create or update exercise content for a given locale.',
        summary    : 'Upsert exercise localized content.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                required  : ['name', 'summary'],
                properties: [
                    new OAT\Property(property: 'name', type: 'string', example: 'Lorem ipsum'),
                    new OAT\Property(property: 'summary', type: 'string', example: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.'),
                    new OAT\Property(property: 'details', type: 'string', nullable: true),
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Content updated.'),
            new OAT\Response(response: 201, description: 'Content created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Type not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContent(
        string                      $wodTypeId,
        string                      $locale,
        Request                     $request,
        UpsertContentWodTypeUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodTypeHttp(
                    id     : Uuid::fromString($wodTypeId),
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
        path        : '/{wodTypeId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'wodTypeId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_TYPE_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an WOD Type.',
        summary    : 'Upsert multiple WOD Type contents.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            required: true,
            content : new OAT\JsonContent(
                example: [
                    "fr" => [
                        "name"    => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                        "details" => "",
                    ],
                    "en" => [
                        "name"    => "Lorem ipsum",
                        "summary" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, in nec purus fringilla.",
                        "details" => "",
                    ],
                ]
            )
        ),
        responses  : [
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Type not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                          $wodTypeId,
        Request                         $request,
        UpsertContentWodTypeBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodTypeBulkHttp(
                    id     : Uuid::fromString($wodTypeId),
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
