<?php

namespace App\UI\Controller\Security;

use App\Domain\Security\Entity\Permission;
use App\Domain\Security\Permission\GetPermissionByIdUseCase;
use App\Domain\Security\Permission\ListPermissionUseCase;
use App\Domain\Security\Service\PermissionService;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\Security\Permission\GetPermissionByIdHttp;
use App\UI\Adapters\Http\Security\Permission\ListPermissionHttp;
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
#[Route(path: '/permissions', name: 'permission_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final readonly class PermissionController
{
    use ApiExceptionHandler;

    public function __construct(
        private PermissionService $permissionService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PERMISSION_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of permission available in the system.',
        summary    : 'List of permissions',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of permissions',
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
                            type  : Permission::class,
                            groups: [FrontGroupsEnum::ROLE_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request               $request,
        ListPermissionUseCase $useCase,
        NormalizerInterface   $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        try {
            $paginator = $useCase->execute(
                new ListPermissionHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->permissionService->transformCollectionToDTO(
            permissions: $paginator->getItems(),
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::PERMISSION_LIST,
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
        path        : '/{permissionId}',
        name        : 'detail',
        requirements: [
            'permissionId' => '\d+'
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PERMISSION_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific permission.',
        summary    : 'Get permission details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of permission',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Permission::class,
                        groups: [FrontGroupsEnum::ROLE_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetPermissionByIdUseCase $useCase,
        NormalizerInterface      $normalizer,
        string                   $permissionId
    ): JsonResponse {
        try {
            $permission = $useCase->execute(
                new GetPermissionByIdHttp($permissionId)
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->permissionService->transformToDTO($permission);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::PERMISSION_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }
}
