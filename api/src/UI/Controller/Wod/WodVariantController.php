<?php

namespace App\UI\Controller\Wod;

use App\Domain\Wod\Service\WodVariantService;
use App\Domain\Wod\WodVariant\CreateWodVariantUseCase;
use App\Domain\Wod\WodVariant\DeleteWodVariantUseCase;
use App\Domain\Wod\WodVariant\UpdateWodVariantUseCase;
use App\Infrastructure\Security\Voters\ListPermissions;
use App\Infrastructure\Serialization\FrontGroupsEnum;
use App\UI\Adapters\Http\Wod\WodVariant\CreateWodVariantHttp;
use App\UI\Adapters\Http\Wod\WodVariant\UpdateWodVariantHttp;
use App\UI\Adapters\Http\Common\ApiExceptionHandler;
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
final class WodVariantController extends AbstractController
{
    use ApiExceptionHandler;

    public function __construct(
        private readonly WodVariantService $wodVariantService,
    ) {
    }

    #[Route(
        path   : '/wods/{wodId}/variants',
        name   : 'wod_variant_create',
        methods: ['POST']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_MANAGE)]
    public function create(
        Request                 $request,
        string                  $wodId,
        CreateWodVariantUseCase $useCase,
        NormalizerInterface     $normalizer,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);
            $variant = $useCase->execute(
                new CreateWodVariantHttp($payload, $wodId)
            );

            $dto = $this->wodVariantService->transformToDTO($variant);

            return new JsonResponse(
                data   : $normalizer->normalize(
                    object : $dto,
                    format : 'json',
                    context: ['groups' => [FrontGroupsEnum::WOD_DETAIL]]
                ),
                status : Response::HTTP_CREATED,
                headers: ['X-RESOURCE-ID' => $variant->getId()->toRfc4122()]
            );
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/wod-variants/{variantId}',
        name        : 'wod_variant_update',
        requirements: ['variantId' => '[0-9a-fA-F\-]+'],
        methods     : ['PATCH']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_MANAGE)]
    public function patch(
        Request                 $request,
        string                  $variantId,
        UpdateWodVariantUseCase $useCase,
    ): JsonResponse {
        try {
            $payload = json_decode($request->getContent(), true);
            $useCase->execute(
                new UpdateWodVariantHttp(
                    variantId: Uuid::fromString($variantId),
                    payload  : $payload
                )
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    #[Route(
        path        : '/wod-variants/{variantId}',
        name        : 'wod_variant_delete',
        requirements: ['variantId' => '[0-9a-fA-F\-]+'],
        methods     : ['DELETE']
    )]
    #[IsGranted(ListPermissions::PERMISSION_WOD_MANAGE)]
    public function delete(
        string                  $variantId,
        DeleteWodVariantUseCase $useCase,
    ): JsonResponse {
        try {
            $useCase->execute(
                new class(Uuid::fromString($variantId)) implements \App\Domain\Wod\WodVariant\DeleteWodVariantDTOInterface {
                    public function __construct(private readonly Uuid $id)
                    {
                    }

                    public function getVariantId(): Uuid
                    {
                        return $this->id;
                    }
                }
            );

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}
