<?php

namespace App\Infrastructure\ApiPlatform\Wod\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Service\WodService;
use App\Infrastructure\ApiPlatform\Exception\ApiException;
use App\Infrastructure\ApiPlatform\Provider\AbstractProvider;
use App\Infrastructure\Persistence\Doctrine\Wod\WodRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

final class WodProvider extends AbstractProvider
{
    public function __construct(
        RequestStack                       $requestStack,
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly WodService        $wodService,
        private readonly WodRepository     $wodRepository,
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

    /** @return Wod[] */
    protected function provideCollection(Operation $operation, array $uriVariables, array $context): array
    {
        $filters = $context['filters'] ?? [];

        /** @var Wod[] $wods */
        $wods = $this->wodRepository->findAll();

        $enriched = [];
        foreach ($wods as $wod) {
            if ($this->applyFilters($wod, $filters)) {
                $enriched[] = $this->wodService->enrichEntity($wod);
            }
        }

        // Si la pagination est désactivée, retourne tout
        if (!$operation->getPaginationEnabled()) {
            return $enriched;
        }

        // Création du paginator à partir du tableau
        $adapter   = new ArrayAdapter($enriched);
        $paginator = new Pagerfanta($adapter);

        // Récupération de la page courante et du nombre d'items par page
        $page                = isset($filters['page']) ? (int)$filters['page'] : 1;
        $defaultItemsPerPage = $operation->getPaginationItemsPerPage();
        $itemsPerPage        = isset($filters['itemsPerPage']) ? (int)$filters['itemsPerPage'] : $defaultItemsPerPage;

        $paginator
            ->setCurrentPage($page)
            ->setMaxPerPage($itemsPerPage);

        return $paginator->getCurrentPageResults();
    }

    protected function provideItem(Operation $operation, array $uriVariables, array $context): mixed
    {
        $wodId = $uriVariables['id'] ?? null;
        if (!$wodId) {
            throw new ApiException('wod.required', 400);
        }

        /** @var Wod|null $wod */
        $wod = $this->getItemProvider()->provide($operation, $uriVariables, $context);
        if (!$wod instanceof Wod) {
            throw new ApiException('wod.not_found', 404);
        }

        return $this->wodService->enrichEntity($wod);
    }

    private function applyFilters(Wod $wod, array $filters): bool
    {
        $genderFilter      = $filters['wodVersions.wodVersionVariants.gender'] ?? null;
        $timeCapFilter     = $filters['wodVersions.wodVersionVariants.timeCap'] ?? null;
        $searchFilter      = $filters['search'] ?? null;
        $titleFilter       = $filters['title'] ?? null;
        $descriptionFilter = $filters['description'] ?? null;

        $equipmentsIn    = $filters['equipments']['in'] ?? null;
        $equipmentsNotIn = $filters['equipments']['notIn'] ?? null;

        $movementsIn    = $filters['movements']['in'] ?? null;
        $movementsNotIn = $filters['movements']['notIn'] ?? null;

        $musclesIn    = $filters['muscles']['in'] ?? null;
        $musclesNotIn = $filters['muscles']['notIn'] ?? null;

        $keepWod = false;

        foreach ($wod->getWodVersions() as $version) {
            $variants = $version->getWodVersionVariants();

            // Gender filter
            // ---------------------------------------------------------------------------------------------------------
            if ($genderFilter) {
                $variants = $variants->filter(fn($v) => $v->getGender()?->value === $genderFilter);
            }

            // TimeCap filter
            // ---------------------------------------------------------------------------------------------------------
            if ($timeCapFilter && count($variants) > 0) {
                $variants = $variants->filter(function ($v) use ($timeCapFilter) {
                    $tc = $v->getTimeCap();
                    if (isset($timeCapFilter['gte']) && $tc < $timeCapFilter['gte']) return false;
                    if (isset($timeCapFilter['lte']) && $tc > $timeCapFilter['lte']) return false;
                    if (isset($timeCapFilter['gt']) && $tc <= $timeCapFilter['gt']) return false;
                    if (isset($timeCapFilter['lt']) && $tc >= $timeCapFilter['lt']) return false;
                    if (isset($timeCapFilter['between'])) {
                        [$min, $max] = $timeCapFilter['between'];
                        if ($tc < $min || $tc > $max) return false;
                    }
                    return true;
                });
            }

            // Equipments filter
            // ---------------------------------------------------------------------------------------------------------
            if (($equipmentsIn || $equipmentsNotIn) && count($variants) > 0) {
                $variants = $variants->filter(function ($v) use ($equipmentsIn, $equipmentsNotIn) {
                    $equipmentIds = [];
                    foreach ($v->getWodVersionVariantMovements() as $wvvm) {
                        $movement = $wvvm->getMovement();
                        if ($movement && $movement->getEquipment()) {
                            $equipmentIds[] = $movement->getEquipment()->getId();
                        }
                    }

                    if ($equipmentsIn && count(array_intersect($equipmentsIn, $equipmentIds)) === 0) {
                        return false;
                    }

                    if ($equipmentsNotIn && count(array_intersect($equipmentsNotIn, $equipmentIds)) > 0) {
                        return false;
                    }

                    return true;
                });
            }

            // Movements filter
            // ---------------------------------------------------------------------------------------------------------
            if (($movementsIn || $movementsNotIn) && count($variants) > 0) {
                $variants = $variants->filter(function ($v) use ($movementsIn, $movementsNotIn) {
                    $movementIds = [];
                    foreach ($v->getWodVersionVariantMovements() as $wvvm) {
                        $movement = $wvvm->getMovement();
                        if ($movement) {
                            $movementIds[] = $movement->getId();
                        }
                    }

                    if ($movementsIn && count(array_intersect($movementsIn, $movementIds)) === 0) {
                        return false;
                    }

                    if ($movementsNotIn && count(array_intersect($movementsNotIn, $movementIds)) > 0) {
                        return false;
                    }

                    return true;
                });
            }

            // Muscles filter
            // ---------------------------------------------------------------------------------------------------------
            if (($musclesIn || $musclesNotIn) && count($variants) > 0) {
                $variants = $variants->filter(function ($v) use ($musclesIn, $musclesNotIn) {
                    $muscleIds = [];
                    foreach ($v->getWodVersionVariantMovements() as $wvvm) {
                        foreach ($wvvm->getMovement()->getMuscles() as $muscle) {
                            $muscleIds[] = $muscle->getId();
                        }
                    }

                    if ($musclesIn && count(array_intersect($musclesIn, $muscleIds)) === 0) {
                        return false;
                    }

                    if ($musclesNotIn && count(array_intersect($musclesNotIn, $muscleIds)) > 0) {
                        return false;
                    }

                    return true;
                });
            }

            $version->setWodVersionVariants($variants);

            if (count($variants) > 0) {
                $keepWod = true;
            }
        }

        if (!$keepWod) {
            return false; // No valid variant → excluded
        }

        $wodTitle        = method_exists($wod, 'getTitle') ? $wod->getTitle() : '';
        $wodDescriptions = method_exists($wod, 'getDescriptions') ? $wod->getDescriptions() : [];

        if ($searchFilter) {
            $textMatches = stripos($wodTitle, $searchFilter) !== false;
            if (!$textMatches) {
                foreach ($wodDescriptions as $description) {
                    if (stripos($description, $searchFilter) !== false) {
                        $textMatches = true;
                        break;
                    }
                }
            }
            if (!$textMatches) return false;
        }

        if ($titleFilter || $descriptionFilter) {
            $titleMatches       = $titleFilter && stripos($wodTitle, $titleFilter) !== false;
            $descriptionMatches = false;
            if ($descriptionFilter) {
                foreach ($wodDescriptions as $description) {
                    if (stripos($description, $descriptionFilter) !== false) {
                        $descriptionMatches = true;
                        break;
                    }
                }
            }
            if (!$titleMatches && !$descriptionMatches) {
                return false;
            }
        }

        return true;
    }
}
