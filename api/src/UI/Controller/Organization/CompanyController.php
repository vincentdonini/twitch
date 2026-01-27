<?php

namespace App\UI\Controller\Organization;

use App\Domain\Core\Exceptions\AlreadyExistException;
use App\Domain\Core\Exceptions\EntityNotFoundException;
use App\Domain\Organization\Entity\Company;
use App\Domain\Organization\Filters\CompanyFilterMapping;
use App\Domain\Organization\Filters\CompanyFilterRules;
use App\Domain\Organization\Service\CompanyService;
use App\Domain\Organization\Sort\CompanySortMapping;
use App\Domain\Organization\Company\CreateCompanyUseCase;
use App\Domain\Organization\Company\GetCompanyByIdUseCase;
use App\Domain\Organization\Company\ListCompanyUseCase;
use App\Domain\Organization\Company\UpdateCompanyUseCase;
use App\Infrastructure\Filters\RequestFilters;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\Infrastructure\Sorts\RequestSort;
use App\UI\Adapters\Http\Organization\Company\CreateCompanyHttp;
use App\UI\Adapters\Http\Organization\Company\GetCompanyByIdHttp;
use App\UI\Adapters\Http\Organization\Company\ListCompaniesHttp;
use App\UI\Adapters\Http\Organization\Company\UpdateCompanyHttp;
use InvalidArgumentException;
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
#[Route(path: '/companies', name: 'company_')]
final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyService $companyService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_COMPANY_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of companies available in the system.',
        summary    : 'List of companies',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),

            // ---------------------------------------------------------------------------------------------------------
            // slug
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[slug][eq]',
                description: 'Filter by company slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // name
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[name][eq]',
                description: 'Filter by company name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[name][like]',
                description: 'Filter by company name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // legalName
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[legalName][eq]',
                description: 'Filter by company legalName (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[legalName][like]',
                description: 'Filter by company legalName (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // siren
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[siren][eq]',
                description: 'Filter by company siren (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // activityCode
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[activityCode][eq]',
                description: 'Filter by company activityCode (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // vatNumber
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[vatNumber][eq]',
                description: 'Filter by company vatNumber (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // legalForm
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[legalForm][eq]',
                description: 'Filter by company legalForm (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // address
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[address][like]',
                description: 'Filter by company address (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // postalCode
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[postalCode][eq]',
                description: 'Filter by company postalCode (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // city
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[city.slug][eq]',
                description: 'Filter by company city (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[city.name][eq]',
                description: 'Filter by company city (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[city.name][like]',
                description: 'Filter by company city (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // department
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[department.code][eq]',
                description: 'Filter by company department code (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[department.slug][eq]',
                description: 'Filter by company department slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[department.name][eq]',
                description: 'Filter by company department name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[department.name][like]',
                description: 'Filter by company department name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // region
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[region.code][eq]',
                description: 'Filter by company region code (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[region.slug][eq]',
                description: 'Filter by company region slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[region.name][eq]',
                description: 'Filter by company region name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[region.name][like]',
                description: 'Filter by company region name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // country
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[country.slug][eq]',
                description: 'Filter by company country slug (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[country.name][like]',
                description: 'Filter by company country slug (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[country.name][eq]',
                description: 'Filter by company country name (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
            new OAT\Parameter(
                name       : 'filters[country.name][like]',
                description: 'Filter by company country name (partial match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // phone
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[phone][eq]',
                description: 'Filter by company phone (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // email
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[email][eq]',
                description: 'Filter by company email (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),

            // ---------------------------------------------------------------------------------------------------------
            // status
            // ---------------------------------------------------------------------------------------------------------
            new OAT\Parameter(
                name       : 'filters[status][eq]',
                description: 'Filter by company status (exact match)',
                required   : false,
                schema     : new OAT\Schema(type: 'string')
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of companies',
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
                            type  : Company::class,
                            groups: [
                                'PUBLIC',
                                FrontGroupsEnum::COMPANY_LIST_PUBLIC,
                            ]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request             $request,
        ListCompanyUseCase  $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        $allowedFields = CompanyFilterRules::PUBLIC_FIELDS;
        if ($this->isGranted('ROLE_ADMIN')) {
            $allowedFields = array_merge($allowedFields, CompanyFilterRules::ADMIN_FIELDS);
        }

        $filters = RequestFilters::extractValues(
            request         : $request,
            fieldMapping    : CompanyFilterMapping::FIELD_MAP,
            allowedOperators: CompanyFilterRules::ALLOWED_OPERATORS,
            allowedFields   : $allowedFields,
        );

        $sorts = RequestSort::extractValues(
            request    : $request,
            fieldMap   : CompanySortMapping::FIELD_MAP,
            defaultSort: 'name'
        );

        try {
            $paginator = $useCase->execute(
                new ListCompaniesHttp(
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                    filters: $filters,
                    sorts  : $sorts,
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->companyService->transformCollectionToDTO(
            companies: $paginator->getItems(),
            filters  : $filters
        );

        $groups = ['PUBLIC', FrontGroupsEnum::COMPANY_LIST_PUBLIC];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups = ['ADMIN', FrontGroupsEnum::COMPANY_LIST_ADMIN];
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
    #[IsGranted(ListPermissions::PERMISSION_COMPANY_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Post(
        description: 'Creates a new company and returns the created resource ID.',
        summary    : 'Create a company',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Created a company',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Company::class,
                    groups: [FrontGroupsEnum::COMPANY_LIST_PUBLIC]
                )
            )
        ),
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_CREATED,
                description: 'Company created successfully',
                headers    : [
                    new OAT\Header(
                        header     : 'X-RESOURCE-ID',
                        description: 'ID of company created',
                        schema     : new OAT\Schema(type: 'integer')
                    ),
                ],
                content    : new OAT\JsonContent(
                    ref: new Model(
                        type  : Company::class,
                        groups: [FrontGroupsEnum::COMPANY_LIST_PUBLIC]
                    )
                )
            ),
        ]
    )]
    public function create(
        Request              $request,
        CreateCompanyUseCase $useCase,
        NormalizerInterface  $normalizer,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);
            $company = $useCase->execute(
                new CreateCompanyHttp($payload)
            );

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $company,
                    format : 'json',
                    context: [
                        'groups' => [FrontGroupsEnum::COMPANY_MANAGE],
                    ]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => (string)$company->getId()]
            );
        } catch (InvalidArgumentException) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        } catch (AlreadyExistException) {
            $statusCode = Response::HTTP_CONFLICT;
        }

        return new JsonResponse(null, $statusCode);
    }

    #[Route(
        path        : '/{companyId}',
        name        : 'detail',
        requirements: [
            'companyId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_COMPANY_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific company.',
        summary    : 'Get company details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of company',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Company::class,
                        groups: [FrontGroupsEnum::COMPANY_DETAIL_PUBLIC]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetCompanyByIdUseCase $useCase,
        NormalizerInterface   $normalizer,
        string                $companyId
    ): JsonResponse {
        try {
            $company = $useCase->execute(
                new GetCompanyByIdHttp(
                    id: Uuid::fromString($companyId),
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->companyService->transformToDTO($company);

        $groups = ['PUBLIC', FrontGroupsEnum::COMPANY_DETAIL_PUBLIC];

        if ($this->isGranted('ROLE_ADMIN')) {
            $groups = ['ADMIN', FrontGroupsEnum::COMPANY_DETAIL_ADMIN];
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
        path   : '/{companyId}',
        name   : 'update',
        methods: ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_COMPANY_MANAGE)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Patch(
        description: 'Updates an existing company with the provided data.',
        summary    : 'Update a company',
        security   : [['bearerAuth' => []]],
        requestBody: new OAT\RequestBody(
            description: 'Update a company',
            required   : true,
            content    : new OAT\JsonContent(
                ref: new Model(
                    type  : Company::class,
                    groups: [FrontGroupsEnum::COMPANY_LIST_PUBLIC]
                ),
            ),
        ),
        parameters : [
            new OAT\PathParameter(
                name       : 'name',
                description: 'name of the company',
                required   : true,
                schema     : new OAT\Schema(type: 'string'),
            ),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_NO_CONTENT,
                description: 'Company updated successfully',
            ),
        ]
    )]
    public function patch(
        Request              $request,
        string               $companyId,
        UpdateCompanyUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdateCompanyHttp(
                    id     : Uuid::fromString($companyId),
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
