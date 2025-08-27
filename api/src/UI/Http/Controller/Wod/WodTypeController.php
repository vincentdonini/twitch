<?php

namespace App\UI\Http\Controller\Wod;

use App\Domain\Wod\Repository\WodTypeRepositoryInterface;
use App\Domain\Wod\Service\WodTypeService;
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
#[Route('/wod-types', name: 'wod_type_')]
final class WodTypeController extends BaseController
{
    public function __construct(
        private readonly WodTypeRepositoryInterface $wodTypeRepository,
        private readonly WodTypeService             $wodTypeService,
        protected TranslatorInterface               $translator,
        protected SerializerInterface               $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        summary  : 'Get WOD type list',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'WOD type list'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $wodTypes = $this->wodTypeRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($wodType) => $this->wodTypeService->transformToDTO($wodType), $wodTypes),
            serializationGroups: ['wodType:list'],
        );
    }

    #[Route('/{wodTypeId}', name: 'detail', requirements: ['wodTypeId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        summary  : 'Get type details by ID',
        security : [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Get WOD type details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'WOD type not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $wodTypeId): JsonResponse
    {
        $wodType = $this->wodTypeRepository->findOneById($wodTypeId);
        if (!$wodType) {
            return $this->errorJsonResponse(
                'wod.type.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->wodTypeService->transformToDTO($wodType),
            serializationGroups: ['wodType:detail']
        );
    }
}
