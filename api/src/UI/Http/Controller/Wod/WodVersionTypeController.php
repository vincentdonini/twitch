<?php

namespace App\UI\Http\Controller\Wod;

use App\Domain\Wod\Repository\WodVersionTypeRepositoryInterface;
use App\Domain\Wod\Service\WodVersionTypeService;
use App\UI\Http\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsController]
#[Route('/wod-version-types', name: 'wod_version_type_')]
final class WodVersionTypeController extends BaseController
{
    public function __construct(
        private readonly WodVersionTypeRepositoryInterface $wodVersionTypeRepository,
        private readonly WodVersionTypeService             $wodVersionTypeService,
        protected TranslatorInterface                      $translator,
        protected SerializerInterface                      $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        summary  : 'Get WOD version type list',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'WOD version type list'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $wodVersionTypes = $this->wodVersionTypeRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($wodVersionType) => $this->wodVersionTypeService->transformToDTO($wodVersionType), $wodVersionTypes),
            serializationGroups: ['wodVersionType:list']
        );
    }

    #[Route('/{wodVersionTypeId}', name: 'detail', requirements: ['wodVersionTypeId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        summary  : 'Get WOD version type details by ID',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Get WOD version type details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'WOD version type not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $wodVersionTypeId): JsonResponse
    {
        $wodVersionType = $this->wodVersionTypeRepository->findOneById($wodVersionTypeId);
        if (!$wodVersionType) {
            return $this->errorJsonResponse(
                'wod.type.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->wodVersionTypeService->transformToDTO($wodVersionType),
            serializationGroups: ['wodVersionType:detail']
        );
    }
}
