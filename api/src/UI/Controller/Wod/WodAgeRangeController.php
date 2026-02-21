<?php

namespace App\UI\Controller\Wod;

use App\Domain\Wod\Service\WodAgeRangeService;
use App\Domain\Wod\WodAgeRange\GetWodAgeRangeByIdUseCase;
use App\Domain\Wod\WodAgeRange\GetWodAgeRangeContentsByIdUseCase;
use App\Domain\Wod\WodAgeRange\ListWodAgeRangesUseCase;
use App\Domain\Wod\WodAgeRange\UpsertContentWodAgeRangeBulkUseCase;
use App\Domain\Wod\WodAgeRange\UpsertContentWodAgeRangeUseCase;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Wod\WodAgeRange\GetWodAgeRangeByIdHttp;
use App\UI\Adapters\Http\Wod\WodAgeRange\ListWodAgeRangesHttp;
use App\UI\Adapters\Http\Wod\WodAgeRange\UpsertContentWodAgeRangeBulkHttp;
use App\UI\Adapters\Http\Wod\WodAgeRange\UpsertContentWodAgeRangeHttp;
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
#[Route(path: '/wod-age-ranges', name: 'wod_age_range_')]
final readonly class WodAgeRangeController
{
    public function __construct(
        private WodAgeRangeService $wodAgeRangeService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_AGE_RANGE_LIST)]
    #[OAT\Get(
        description: 'Returns a list of all WOD age ranges available in the system.',
        summary    : 'Retrieve all WOD age ranges',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(response: 200, description: 'List of WOD age ranges'),
            new OAT\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function list(
        Request                 $request,
        ListWodAgeRangesUseCase $useCase,
        NormalizerInterface     $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues($request);

        try {
            $paginator = $useCase->execute(
                new ListWodAgeRangesHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->wodAgeRangeService->transformCollectionToDTO($paginator->getItems());

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_AGE_RANGE_LIST,
                    ],
                ]
            ),
            status : Response::HTTP_OK,
            headers: ResponsePaginator::buildPaginationHeaders(paginator: $paginator, paginatorValues: $paginatorValues)
        );
    }

    #[Route(
        path        : '/{wodAgeRangeId}',
        name        : 'detail',
        requirements: [
            'wodAgeRangeId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_AGE_RANGE_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific WOD age range.',
        summary    : 'Get WOD age range details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'WOD age range details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'WOD age range not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function detail(
        GetWodAgeRangeByIdUseCase $useCase,
        NormalizerInterface       $normalizer,
        string                    $wodAgeRangeId
    ): JsonResponse {
        try {
            $wodAgeRange = $useCase->execute(
                new GetWodAgeRangeByIdHttp($wodAgeRangeId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->wodAgeRangeService->transformToDTO($wodAgeRange);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::WOD_AGE_RANGE_LIST,
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
        path        : '/{wodAgeRangeId}/contents',
        name        : 'contents_list',
        requirements: [
            'wodAgeRangeId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_AGE_RANGE_LIST)]
    #[OAT\Get(
        description: 'Retrieve all localized contents for an WOD age range',
        summary    : 'Get WOD age range contents',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Contents retrieved'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'WOD age range not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function listContents(
        string                            $wodAgeRangeId,
        NormalizerInterface               $normalizer,
        GetWodAgeRangeContentsByIdUseCase $useCase
    ): JsonResponse {
        try {
            $wodAgeRangeContents = $useCase->execute(
                new GetWodAgeRangeByIdHttp($wodAgeRangeId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $contentsArray = [];
        foreach ($wodAgeRangeContents as $content) {
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
                        FrontGroupsEnum::CONTENT_WOD_AGE_RANGE_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{wodAgeRangeId}/contents/{locale}',
        name        : 'content_upsert',
        requirements: [
            'wodAgeRangeId' => '\d+',
            'locale'        => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_AGE_RANGE_MANAGE)]
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
        string                          $wodAgeRangeId,
        string                          $locale,
        Request                         $request,
        UpsertContentWodAgeRangeUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodAgeRangeHttp(
                    id     : $wodAgeRangeId,
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
        path        : '/{wodAgeRangeId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'wodAgeRangeId' => '\d+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_WOD_AGE_RANGE_MANAGE)]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an WOD age range',
        summary    : 'Upsert multiple WOD age range contents',
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
            new OAT\Response(response: 404, description: 'WOD age range not found'),
        ]
    )]
    #[Security(name: 'bearerAuth')]
    public function upsertContentsBulk(
        string                              $wodAgeRangeId,
        Request                             $request,
        UpsertContentWodAgeRangeBulkUseCase $useCase
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpsertContentWodAgeRangeBulkHttp(
                    id     : $wodAgeRangeId,
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
