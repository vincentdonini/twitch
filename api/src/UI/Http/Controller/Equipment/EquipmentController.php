<?php

namespace App\UI\Http\Controller\Equipment;

use App\Domain\Equipment\Repository\EquipmentRepositoryInterface;
use App\Domain\Equipment\Service\EquipmentService;
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
#[Route('/equipments', name: 'equipment_')]
final class EquipmentController extends BaseController
{
    public function __construct(
        private readonly EquipmentRepositoryInterface $equipmentRepository,
        private readonly EquipmentService             $equipmentService,
        protected TranslatorInterface                 $translator,
        protected SerializerInterface                 $serializer,
    ) {
        parent::__construct($translator, $serializer);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Get(
        description: 'Returns a list of all equipment available in the system.',
        summary    : 'Retrieve all equipments',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'List of equipment'),
            new OA\Response(response: 403, description: 'Access denied'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function list(): JsonResponse
    {
        $equipments = $this->equipmentRepository->findAll();

        return $this->listResourceJsonResponse(
            items              : array_map(fn($item) => $this->equipmentService->transformToDTO($item), $equipments),
            serializationGroups: ['equipment:list'],
        );
    }

    #[Route('/{equipmentId}', name: 'detail', requirements: ['equipmentId' => '\d+'], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Get(
        description: 'Returns detailed information for a specific equipment.',
        summary    : 'Get equipment details',
        security   : [['bearerAuth' => []]],
        responses  : [
            new OA\Response(response: 200, description: 'Equipment details'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'Equipment not found'),
        ]
    )]
    #[ApiSecurity(name: 'bearerAuth')]
    public function detail(string $equipmentId): JsonResponse
    {
        $equipment = $this->equipmentRepository->findOneById($equipmentId);
        if (!$equipment) {
            return $this->errorJsonResponse(
                'equipment.not_found',
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->singleResourceJsonResponse(
            item               : $this->equipmentService->transformToDTO($equipment),
            serializationGroups: ['equipment:detail'],
        );
    }
}
