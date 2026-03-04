<?php

namespace App\UI\Controller\Organization;

use App\Domain\Organization\Entity\Place;
use App\Domain\Organization\Filters\PlaceFilterMapping;
use App\Domain\Organization\Filters\PlaceFilterRules;
use App\Domain\Organization\Place\CreatePlaceUseCase;
use App\Domain\Organization\Place\GetPlaceByIdUseCase;
use App\Domain\Organization\Place\ImportUsersForPlaceResult;
use App\Domain\Organization\Place\ImportUsersForPlaceUseCase;
use App\Domain\Organization\Place\ListPlaceUseCase;
use App\Domain\Organization\Place\UpdatePlaceUseCase;
use App\Domain\Organization\Service\PlaceService;
use App\Domain\Organization\Sort\PlaceSortMapping;
use App\Domain\User\Service\UserService;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Organization\Place\CreatePlaceHttp;
use App\UI\Adapters\Http\Organization\Place\GetPlaceByIdHttp;
use App\UI\Adapters\Http\Organization\Place\ImportUsersForPlaceHttp;
use App\UI\Adapters\Http\Organization\Place\ListPlacesHttp;
use App\UI\Adapters\Http\Organization\Place\UpdatePlaceHttp;
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
#[Route(path: '/places', name: 'place_')]
final class PlaceController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly PlaceService $placeService,
        private readonly UserService  $userService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PLACE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Return a list of places available in the system.',
        summary    : 'List of places.',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // company
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[company.id][eq]',
                description: 'Filter by company ID (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'integer')
            ),
            new OAT\Parameter(
                name       : 'filters[company.slug][eq]',
                description: 'Filter by company slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[company.name][eq]',
                description: 'Filter by company name (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[company.name][like]',
                description: 'Filter by company name (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[company.legalName][eq]',
                description: 'Filter by company legalName (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[company.legalName][like]',
                description: 'Filter by company legalName (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // slug
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by place slug (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // name
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[name][eq]',
                description: 'Filter by place name (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[name][like]',
                description: 'Filter by place name (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // legalName
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[legalName][eq]',
                description: 'Filter by place legalName (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[legalName][like]',
                description: 'Filter by place legalName (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // siret
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[siret][eq]',
                description: 'Filter by place siret (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // address
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[address][like]',
                description: 'Filter by place address (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // postalCode
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[postalCode][eq]',
                description: 'Filter by place postalCode (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // city
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[city.slug][eq]',
                description: 'Filter by place city (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[city.name][eq]',
                description: 'Filter by place city (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[city.name][like]',
                description: 'Filter by place city (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // department
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[department.code][eq]',
                description: 'Filter by place department code (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[department.slug][eq]',
                description: 'Filter by place department slug (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[department.name][eq]',
                description: 'Filter by place department name (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[department.name][like]',
                description: 'Filter by place department name (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // region
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[region.code][eq]',
                description: 'Filter by place region code (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[region.slug][eq]',
                description: 'Filter by place region slug (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[region.name][eq]',
                description: 'Filter by place region name (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[region.name][like]',
                description: 'Filter by place region name (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // country
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[country.slug][eq]',
                description: 'Filter by place country slug (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[country.name][eq]',
                description: 'Filter by place country name (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[country.name][like]',
                description: 'Filter by place country name (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // phone
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[phone][eq]',
                description: 'Filter by place phone (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // email
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[email][eq]',
                description: 'Filter by place email (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // website
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[website][like]',
                description: 'Filter by place website (partial match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // status
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[status][eq]',
                description: 'Filter by place status (exact match).',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of places',
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
                            type  : Place::class,
                            groups: [FrontGroupsEnum::PLACE_LIST_PUBLIC]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request             $request,
        ListPlaceUseCase    $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $allowedFields = PlaceFilterRules::PUBLIC_FIELDS;
        if ($this->isGranted('ROLE_ADMIN')) {
            $allowedFields = array_merge($allowedFields, PlaceFilterRules::ADMIN_FIELDS);
        }

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : PlaceFilterMapping::FIELD_MAP,
            allowedOperators: PlaceFilterRules::ALLOWED_OPERATORS,
            allowedFields   : $allowedFields,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : PlaceSortMapping::FIELD_MAP,
            defaultSort: 'legalName'
        );

        try {
            $paginator = $useCase->execute(
                new ListPlacesHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->placeService->transformCollectionToDTO(
            places : $paginator->getItems(),
            filters: $filters
        );

        $groups = ['PUBLIC', FrontGroupsEnum::PLACE_LIST_PUBLIC];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups = ['ADMIN', FrontGroupsEnum::PLACE_LIST_ADMIN];
        }

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => $groups,
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
    #[IsGranted(ListPermissions::PERMISSION_PLACE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Create a new place and returns the created resource ID.',
        summary    : 'Create a place.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a place',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Place::class,
                    groups: [FrontGroupsEnum::PLACE_LIST_PUBLIC]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Place created successfully.',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of place created.',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : Place::class,
                        groups: [FrontGroupsEnum::PLACE_LIST_PUBLIC]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request             $request,
        CreatePlaceUseCase  $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);
            $place   = $useCase->execute(
                new CreatePlaceHttp($payload)
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $place,
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::PLACE_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $place->getId()]
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{placeId}',
        name        : 'detail',
        requirements: [
            'placeId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PLACE_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Return detailed information for a specific place.',
        summary    : 'Get place details.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of place',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Place::class,
                        groups: [FrontGroupsEnum::PLACE_DETAIL_PUBLIC]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetPlaceByIdUseCase $useCase,
        NormalizerInterface $normalizer,
        string              $placeId
    ): JsonResponse {
        try {
            $place = $useCase->execute(
                new GetPlaceByIdHttp(
                    id: Uuid::fromString($placeId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->placeService->transformToDTO($place);

        $groups = ['PUBLIC', FrontGroupsEnum::PLACE_DETAIL_PUBLIC];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups = ['ADMIN', FrontGroupsEnum::PLACE_DETAIL_ADMIN];
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => $groups,
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '/{placeId}',
        name   : 'update',
        methods: ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PLACE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Patch(
        description: 'Update an existing place with the provided data.',
        summary    : 'Update a place.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Update a place.',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Place::class,
                    groups: [FrontGroupsEnum::PLACE_LIST_PUBLIC]
                ),
            ),
        ),
        parameters : [
            new OAT\PathParameter(
                name       : 'legalName',
                description: 'Legal name of the place.',
                required   : true,
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_NO_CONTENT,
                description: 'Place updated successfully.',
            ),
        ]
    )]
    public function patch(
        Request            $request,
        string             $placeId,
        UpdatePlaceUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdatePlaceHttp(
                    id     : Uuid::fromString($placeId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{placeId}/athletes',
        name        : 'list_athletes',
        requirements: [
            'placeId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PLACE_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Return the list of athletes for a specific place.',
        summary    : 'List place athletes.',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of athletes.',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : Place::class,
                            groups: [FrontGroupsEnum::ATHLETE_LIST]
                        )
                    )
                )
            ),
            new OAT\Response(
                response   : Response::HTTP_NOT_FOUND,
                description: 'Place not found.'
            ),
        ],
    )]
    public function listAthletes(
        GetPlaceByIdUseCase $useCase,
        NormalizerInterface $normalizer,
        string              $placeId
    ): JsonResponse {
        try {
            $place = $useCase->execute(
                new GetPlaceByIdHttp(
                    id: Uuid::fromString($placeId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->userService->transformCollectionToDTO($place->getUsers()->toArray());

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [FrontGroupsEnum::ATHLETE_LIST],
                ]
            ),
            status: Response::HTTP_OK
        );
    }

    #[Route(
        path        : '/{placeId}/athletes/import',
        name        : 'import_athletes',
        requirements: [
            'placeId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['POST']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PLACE_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Import users from a CSV and attach them to a place.',
        summary    : 'Import users for a place.',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'CSV upload',
            required   : true,
            content    : new OAT\MediaType(
                mediaType: 'multipart/form-data',
                schema   : new OAT\Schema(
                    required  : ['file'],
                    properties: [
                        new OAT\Property(
                            property   : 'file',
                            description: 'CSV file containing users.',
                            type       : 'string',
                            format     : 'binary'
                        ),
                    ],
                    type      : 'object'
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Represent the result of importing users for a place, including created, existing, attached users and any errors.',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type: ImportUsersForPlaceResult::class,
                    ),
                    type: 'object'
                )
            ),
            new OAT\Response(
                response   : Response::HTTP_BAD_REQUEST,
                description: 'Bad request.'
            ),
            new OAT\Response(
                response   : Response::HTTP_NOT_FOUND,
                description: 'Place not found.'
            ),
        ]
    )]
    public function import(
        Request                    $request,
        ImportUsersForPlaceUseCase $useCase,
        NormalizerInterface        $normalizer,
        string                     $placeId,
    ): JsonResponse {
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $result = $useCase->execute(
                new ImportUsersForPlaceHttp(
                    id     : Uuid::fromString($placeId),
                    csvPath: $file->getRealPath()
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        return new JsonResponse(
            data  : $normalizer->normalize(
                object: $result,
                format: 'json'
            ),
            status: Response::HTTP_OK,
        );
    }
}
