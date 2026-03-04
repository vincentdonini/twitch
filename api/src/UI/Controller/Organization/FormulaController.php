<?php

namespace App\UI\Controller\Organization;

use App\Domain\Organization\Formula\CreateFormulaUseCase;
use App\Domain\Organization\Formula\DeleteFormulaUseCase;
use App\Domain\Organization\Formula\GetFormulaByIdByPlaceIdUseCase;
use App\Domain\Organization\Formula\ListFormulasByPlaceIdUseCase;
use App\Domain\Organization\Formula\UpdateFormulaUseCase;
use App\Domain\Organization\Service\FormulaService;
use App\Infrastructure\Paginator\RequestPaginator;
use App\Infrastructure\Paginator\ResponsePaginator;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
use App\UI\Adapters\Http\Organization\Formula\CreateFormulaHttp;
use App\UI\Adapters\Http\Organization\Formula\DeleteFormulaHttp;
use App\UI\Adapters\Http\Organization\Formula\GetFormulaByIdByPlaceIdHttp;
use App\UI\Adapters\Http\Organization\Formula\ListFormulasByPlaceIdHttp;
use App\UI\Adapters\Http\Organization\Formula\UpdateFormulaHttp;
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
#[Route(
    path        : '/places/{placeId}/formulas',
    name        : 'formula_',
    requirements: [
        'placeId' => '[0-9a-fA-F\-]+',
    ],
)]
final class FormulaController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly FormulaService $formulaService,
    ) {
    }

    #[Route(
        path   : '',
        name   : 'list',
        methods: ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_FORMULA_LIST)]
    public function list(
        Request                      $request,
        ListFormulasByPlaceIdUseCase $useCase,
        NormalizerInterface          $normalizer,
        string                       $placeId,
    ): JsonResponse {
        $paginatorValues = RequestPaginator::extractValues(
            request: $request
        );

        try {
            $paginator = $useCase->execute(
                new ListFormulasByPlaceIdHttp(
                    placeId: Uuid::fromString($placeId),
                    page   : $paginatorValues->getPage(),
                    limit  : $paginatorValues->getLimit(),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItems = $this->formulaService->transformCollectionToDTO(
            formulas: $paginator->getItems(),
        );

        return new JsonResponse(
            data   : $normalizer->normalize(
                object : $dtoItems,
                format : 'json',
                context: [
                    'groups' => [FrontGroupsEnum::FORMULA_LIST],
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
        path        : '/{formulaId}',
        name        : 'detail',
        requirements: [
            'formulaId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['GET']
    )]
    #[IsGranted(ListPermissions::PERMISSION_FORMULA_VIEW)]
    public function detail(
        GetFormulaByIdByPlaceIdUseCase $useCase,
        NormalizerInterface            $normalizer,
        string                         $formulaId,
        string                         $placeId,
    ): JsonResponse {
        try {
            $formula = $useCase->execute(
                new GetFormulaByIdByPlaceIdHttp(
                    id     : Uuid::fromString($formulaId),
                    placeId: Uuid::fromString($placeId),
                )
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }

        $dtoItem = $this->formulaService->transformToDTO($formula);

        return new JsonResponse(
            data  : $normalizer->normalize(
                object : $dtoItem,
                format : 'json',
                context: [
                    'groups' => [FrontGroupsEnum::FORMULA_DETAIL],
                ]
            ),
            status: Response::HTTP_OK,
        );
    }

    #[Route(
        path   : '',
        name   : 'create',
        methods: ['POST']
    )]
    #[IsGranted(ListPermissions::PERMISSION_FORMULA_MANAGE)]
    public function create(
        CreateFormulaUseCase $useCase,
        Request              $request,
        NormalizerInterface  $normalizer,
        string               $placeId,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $formula = $useCase->execute(
                new CreateFormulaHttp(
                    placeId: Uuid::fromString($placeId),
                    payload: $payload
                )
            );

            $dtoItem = $this->formulaService->transformToDTO($formula);

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $dtoItem,
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::FORMULA_MANAGE]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $formula->getId()->toRfc4122()]
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{formulaId}',
        name        : 'update',
        requirements: [
            'formulaId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_FORMULA_MANAGE)]
    public function update(
        UpdateFormulaUseCase $useCase,
        Request              $request,
        string               $formulaId,
        string               $placeId,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);

            $useCase->execute(
                new UpdateFormulaHttp(
                    id     : Uuid::fromString($formulaId),
                    placeId: Uuid::fromString($placeId),
                    payload: $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/{formulaId}',
        name        : 'delete',
        requirements: [
            'formulaId' => '[0-9a-fA-F\-]+',
        ],
        methods     : ['DELETE']
    )]
    #[IsGranted(ListPermissions::PERMISSION_FORMULA_MANAGE)]
    public function delete(
        DeleteFormulaUseCase $useCase,
        string               $placeId,
        string               $formulaId,
    ): JsonResponse {
        try {
            $useCase->execute(
                new DeleteFormulaHttp(
                    id     : Uuid::fromString($formulaId),
                    placeId: Uuid::fromString($placeId),
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
