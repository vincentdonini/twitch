<?php

namespace App\UI\Controller\Security;

use App\Application\Security\DTO\PermissionDTO;
use App\Domain\Security\Entity\Role;
use App\Domain\Security\Role\GetRoleByIdUseCase;
use App\Domain\Security\Role\listPermissionsByRoleUseCase;
use App\Domain\Security\Role\ListRolesUseCase;
use App\Domain\Security\Service\PermissionService;
use App\Domain\Security\Service\RoleService;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Security\Role\GetRoleByIdHttp;
use App\UI\Adapters\Http\Security\Role\listPermissionsByRoleHttp;
use App\UI\Adapters\Http\Security\Role\ListRolesHttp;
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
#[Route(path: '/roles', name: 'role_')]
#[OAT\Response(ref: '#/components/responses/BadRequest', response: Response::HTTP_BAD_REQUEST)]
#[OAT\Response(ref: '#/components/responses/Unauthorized', response: Response::HTTP_UNAUTHORIZED)]
#[OAT\Response(ref: '#/components/responses/Forbidden', response: Response::HTTP_FORBIDDEN)]
#[OAT\Response(ref: '#/components/responses/NotFound', response: Response::HTTP_NOT_FOUND)]
final readonly class RoleController
{
    public function __construct(
        private RoleService       $roleService,
        private PermissionService $permissionService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ROLE_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns a list of role available in the system.',
        summary    : 'List of roles',
        security   : [['bearerAuth' => []]],
        parameters : [
            new OAT\Parameter('#/components/parameters/QueryRequestPage'),
            new OAT\Parameter('#/components/parameters/QueryRequestLimit'),
        ],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of roles',
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
                            type  : Role::class,
                            groups: [FrontGroupsEnum::ROLE_LIST]
                        )
                    )
                )
            ),
        ]
    )]
    public function list(
        Request             $request,
        ListRolesUseCase    $useCase,
        NormalizerInterface $normalizer,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        try {
            $paginator = $useCase->execute(
                new ListRolesHttp(
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                )
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->roleService->transformCollectionToDTO(
            roles: $paginator->getItems(),
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ROLE_LIST,
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
        path        : '/{roleId}',
        name        : 'detail',
        requirements: [
            'roleId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_ROLE_VIEW)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns detailed information for a specific role.',
        summary    : 'Get role details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'Detail of role',
                content    : new OAT\JsonContent(
                    ref : new Model(
                        type  : Role::class,
                        groups: [FrontGroupsEnum::ROLE_DETAIL]
                    ),
                    type: 'object'
                )
            ),
        ],
    )]
    public function detail(
        GetRoleByIdUseCase  $useCase,
        NormalizerInterface $normalizer,
        string              $roleId
    ): JsonResponse {
        try {
            $role = $useCase->execute(
                new GetRoleByIdHttp($roleId)
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        }

        $dtoItem = $this->roleService->transformToDTO($role);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ROLE_DETAIL,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path        : '/{roleId}/permissions',
        name        : 'permissions',
        requirements: [
            'roleId' => '\d+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_PERMISSION_LIST)]
    #[Security(name: 'bearerAuth')]
    #[OAT\Get(
        description: 'Returns permissions for a specific role.',
        summary    : 'Get role permissions',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OAT\Response(
                response   : Response::HTTP_OK,
                description: 'List of permissions for role',
                content    : new OAT\JsonContent(
                    type : 'array',
                    items: new OAT\Items(
                        ref: new Model(
                            type  : PermissionDTO::class,
                            groups: [FrontGroupsEnum::ROLE_PERMISSION_LIST]
                        )
                    )
                )
            ),
        ],
    )]
    public function listPermissionsByRole(
        Request                      $request,
        listPermissionsByRoleUseCase $useCase,
        NormalizerInterface          $normalizer,
        string                       $roleId,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        try {
            $paginator = $useCase->execute(
                new listPermissionsByRoleHttp(
                    id   : $roleId,
                    page : $paginatorValues->getPage(),
                    limit: $paginatorValues->getLimit(),
                )
            );
        } catch (EntityNotFoundException) {
            return new JsonResponse(
                data  : null,
                status: Response::HTTP_NOT_FOUND
            );
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException();
        }

        $dtoItems = $this->permissionService->transformCollectionToDTO(
            permissions: $paginator->getItems(),
        );

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [
                        FrontGroupsEnum::ROLE_PERMISSION_LIST,
                    ],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }
}
