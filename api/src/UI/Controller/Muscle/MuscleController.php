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
use App\Domain\Muscle\Service\MuscleService;
use App\Domain\Muscle\Sort\MuscleSortMapping;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Muscle\Muscle\GetMuscleByIdHttp;
use App\UI\Adapters\Http\Muscle\Muscle\ListMusclesHttp;
use App\UI\Adapters\Http\Muscle\Muscle\UpsertContentMuscleBulkHttp;
use App\UI\Adapters\Http\Muscle\Muscle\UpsertContentMuscleHttp;
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

#[AsController]
#[Route(path: '/muscles', name: 'muscle_')]
final readonly class MuscleController
{
    public function __construct(
        private MuscleService $muscleService,
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
        description: 'Returns a list of muscles available in the system.',
        summary    : 'List of muscles',
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
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of muscles',
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
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->muscleService->transformCollectionToDTO(
            muscles: $paginator->getItems(),
            filters: $filters
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
            'muscleId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_MUSCLE_VIEW)]
    #[OAT\Get(
        description: 'Returns detailed information for a specific muscle.',
        summary    : 'Get muscle details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(response: 200, description: 'Muscle details'),
            new OAT\Response(response: 403, description: 'Access denied'),
            new OAT\Response(response: 404, description: 'Muscle not found'),
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
                new GetMuscleByIdHttp($muscleId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
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
            'muscleId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns all localized contents for an muscle',
        summary    : 'List of all muscle contents',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of all muscle contents',
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
                new GetMuscleByIdHttp($muscleId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
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
            'muscleId' => '\d+',
            'locale'   => '[a-z]{2}',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update muscle content for a given locale.',
        summary    : 'Upsert muscle localized content',
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
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Contents upserted successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'Resource ID of the upserted muscle contents'
                    ),
                ]
            ),
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
                    id     : $muscleId,
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
        path        : '/{muscleId}/contents',
        name        : 'contents_upsert_bulk',
        requirements: [
            'muscleId' => '\d+',
        ],
        methods     : ['PUT']
    )]
    #[IsGranted(ListPermissions::PERMISSION_CONTENT_MUSCLE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Put(
        description: 'Create or update multiple localized contents for an muscle',
        summary    : 'Upsert multiple muscle contents',
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
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Contents upserted successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'Resource ID of the upserted contents'
                    ),
                ]
            ),
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
                    id     : $muscleId,
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
