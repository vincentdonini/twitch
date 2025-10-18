<?php

namespace App\Infrastructure\ApiPlatform\Equipment\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Equipment\Entity\Equipment;
use App\Domain\Equipment\Service\EquipmentService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use App\Infrastructure\Persistence\Doctrine\Equipment\EquipmentRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

final class EquipmentProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                         $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface   $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface   $collectionProvider,
        private readonly EquipmentService    $equipmentService,
        private readonly EquipmentRepository $equipmentRepository
    ) {
        parent::__construct($requestStack);
    }

    protected function getItemProvider(): ProviderInterface
    {
        return $this->itemProvider;
    }

    protected function getCollectionProvider(): ProviderInterface
    {
        return $this->collectionProvider;
    }

    /** @return Equipment[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        /** @var $equipments Equipment[] */
        $equipments = $this->equipmentRepository->findAll();

        $enriched = [];
        foreach ($equipments as $equipment) {
            $enriched[] = $this->equipmentService->enrichEntity($equipment);
        }

        return $this->paginate($enriched, $operation, $context);
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $equipmentId = $uriVariables['id'] ?? null;
        if (!$equipmentId) {
            throw new ApiException('equipment.required', 400);
        }

        $equipment = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!$equipment instanceof Equipment) {
            throw new ApiException('equipment.not_found', 404);
        }

        return $this->equipmentService->enrichEntity($equipment);
    }
}