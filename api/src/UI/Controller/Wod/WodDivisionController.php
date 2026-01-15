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
        description: 'Returns a list of all WOD divisions available in the system.',
        summary    : 'Retrieve all WOD divisions',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
            new OAT\Parameter(
                name       : 'slug',
                description: 'Filter result by slug.',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'title',
                description: 'Filter result by title.',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'summary',
                description: 'Filter result by summary.',
                schema     : new OAT\Schema(type: 'string'),
            ),
            new OAT\Parameter(
                name       : 'details',
                description: 'Filter result by details.',
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of WOD divisions'),
            new OAT\Response(response: 403, description: 'Access denied'),
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
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: [
                        'filters' => $request->query->all('filters'),
                    ]
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
            'wodDivisionId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_DIVISION_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific WOD division.',
        summary    : 'Get WOD division details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'WOD division details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'WOD division not found'),
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
                new GetWodDivisionByIdHttp($wodDivisionId)
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
                        FrontGroupsEnum::WOD_DIVISION_LIST,
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
            'wodDivisionId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_DIVISION_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an WOD division',
        summary    : 'Get WOD division contents',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'WOD division not found'),
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
                new GetWodDivisionByIdHttp($wodDivisionId)
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
            'wodDivisionId' => '\d+',
            'locale'        => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_DIVISION_MANAGE)]
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
        string                          $wodDivisionId,
        string                          $locale,
        Request                         $request,
        UpsertContentWodDivisionUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodDivisionHttp(
                    id     : $wodDivisionId,
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
            'wodDivisionId' => '\d+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_DIVISION_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an WOD division',
        summary    : 'Upsert multiple WOD division contents',
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
            new OAT\Response(response: 404, description: 'WOD division not found'),
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
                    id     : $wodDivisionId,
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
