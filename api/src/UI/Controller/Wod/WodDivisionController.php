<?php

namespace App\UI\Controller\Wod;

use App\Domain\Wod\Service\WodDivisionService;
use App\Domain\Wod\WodDivision\GetWodDivisionByIdUseCase;
use App\Domain\Wod\WodDivision\GetWodDivisionContentsByIdUseCase;
use App\Domain\Wod\WodDivision\ListWodDivisionsUseCase;
use App\Domain\Wod\WodDivision\UpsertContentWodDivisionBulkUseCase;
use App\Domain\Wod\WodDivision\UpsertContentWodDivisionUseCase;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Wod\WodDivision\GetWodDivisionByIdHttp;
use App\UI\Adapters\Http\Wod\WodDivision\ListWodDivisionsHttp;
use App\UI\Adapters\Http\Wod\WodDivision\UpsertContentWodDivisionBulkHttp;
use App\UI\Adapters\Http\Wod\WodDivision\UpsertContentWodDivisionHttp;
use App\Domain\Core\Exceptions\EntityNotFoundException;
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
#[Route(path: '/wod-divisions', name: 'wod_division_')]
final readonly class WodDivisionController
{
    public function __construct(
        private WodDivisionService $wodDivisionService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_DIVISION_LIST)]
    #[OAT\Get(
        description: 'Return a list of all WOD Divisions available in the system.',
        summary    : 'List of WOD Divisions.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of WOD Divisions.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function list(
        Request                 $request,
        ListWodDivisionsUseCase $useCase,
        NormalizerInterface     $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues($request);

        try {
            $paginator = $useCase->execute(
                new ListWodDivisionsHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->wodDivisionService->transformCollectionToDTO($paginator->getItems());

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_DIVISION_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{wodDivisionId}',
        name        : 'detail',
        requirements: [
            'wodDivisionId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_DIVISION_VIEW)]
    #[OAT\Get(
        description: 'Return detailed information for a specific WOD Division.',
        summary    : 'Get WOD Division details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'WOD Division details.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Division not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetWodDivisionByIdUseCase $useCase,
        NormalizerInterface       $normalizer,
        string                    $wodDivisionId
    ): JsonResponse {
        try {
            $wodDivision = $useCase->execute(
                new GetWodDivisionByIdHttp(
                    id: Uuid::fromString($wodDivisionId),
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->wodDivisionService->transformToDTO($wodDivision);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_DIVISION_DETAIL,
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
        path        : '/{wodDivisionId}/contents',
        name        : 'contents_list',
        requirements: [
            'wodDivisionId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_DIVISION_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an WOD Division.',
        summary    : 'Get WOD Division contents.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Division not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                            $wodDivisionId,
        NormalizerInterface               $normalizer,
        GetWodDivisionContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $wodTypeContents = $useCase->execute(
                new GetWodDivisionByIdHttp(
                    id: Uuid::fromString($wodDivisionId),
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
                        FrontGroupsEnum::CONTENT_WOD_DIVISION_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{wodDivisionId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'wodDivisionId' => '[0-9a-fA-F\-]+',
            'locale'        => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_DIVISION_MANAGE)]
    #[OAT\Put(
        description: 'Create or update WOD Division content for a given locale.',
        summary    : 'Upsert WOD Division localized content.',
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
            new OAT\Response(response: 200, description: 'Content updated.'),
            new OAT\Response(response: 201, description: 'Content created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Division not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContent(
        string                          $wodDivisionId,
        string                          $locale,
        Request                         $request,
        UpsertContentWodDivisionUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodDivisionHttp(
                    id     : Uuid::fromString($wodDivisionId),
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
        path        : '/{wodDivisionId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'wodDivisionId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_DIVISION_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an WOD Division.',
        summary    : 'Upsert multiple WOD Division contents.',
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
            new OAT\Response(response: 200, description: 'Contents updated.'),
            new OAT\Response(response: 201, description: 'Contents created.'),
            new OAT\Response(response: 400, description: 'Invalid payload.'),
            new OAT\Response(response: 403, description: 'Access denied.'),
            new OAT\Response(response: 404, description: 'WOD Division not found.'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                              $wodDivisionId,
        Request                             $request,
        UpsertContentWodDivisionBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodDivisionBulkHttp(
                    id     : Uuid::fromString($wodDivisionId),
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
