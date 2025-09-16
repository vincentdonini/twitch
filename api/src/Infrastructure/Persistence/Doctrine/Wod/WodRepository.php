<?php

namespace App\Infrastructure\Persistence\Doctrine\Wod;

use App\Domain\Wod\Entity\Wod;
use App\Domain\Wod\Enum\Gender;
use App\Domain\Wod\Repository\WodRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Common\FilterableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class WodRepository extends ServiceEntityRepository implements WodRepositoryInterface
{
    use FilterableTrait;

    public function __construct(
        ManagerRegistry                         $registry,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($registry, Wod::class);
    }

    public function findOneById(string $id): ?Wod
    {
        return $this->find($id);
    }

    public function findOneByName(string $name): ?Wod
    {
        return $this->find($name);
    }

    public function save(Wod $wod): void
    {
        $this->entityManager->persist($wod);
        $this->entityManager->flush();
    }

    public function delete(Wod $wod): void
    {
        $this->entityManager->remove($wod);
        $this->entityManager->flush();
    }

    protected function applyCustomFilters(QueryBuilder $qb, string $alias, array $filters): void
    {
        $em = $qb->getEntityManager();

        // ---------------- Base joins ----------------
        $qb->leftJoin("$alias.wodVersions", "wv")
            ->leftJoin("wv.wodVersionVariants", "wvv");

        // ---------------- Configuration des filtres classiques ----------------
        $filterConfig = [
            // Wod fields
            'wodTypeIds'        => ['field' => "$alias.wodType", 'operator' => 'IN'],
            'wodCategoryIds'    => ['field' => "$alias.wodCategory", 'operator' => 'IN'],

            // WodVersion fields
            'wodVersionTypeIds' => ['field' => "wv.wodVersionType", 'operator' => 'IN'],

            // WodVersionVariant fields
            'gender'            => [
                'field'     => 'wvv.gender',
                'operator'  => 'IN',
                'converter' => fn($v) => $v instanceof Gender ? $v->value : $v,
            ],
            'timeCapMin'        => ['field' => 'wvv.timeCap', 'operator' => '>='],
            'timeCapMax'        => ['field' => 'wvv.timeCap', 'operator' => '<='],
            'roundsMin'         => ['field' => 'wvv.rounds', 'operator' => '>='],
            'roundsMax'         => ['field' => 'wvv.rounds', 'operator' => '<='],
        ];

        $this->applyFiltersToQueryBuilder($qb, $filters, $filterConfig);

        $em = $qb->getEntityManager();

        // ---------------- Movements allowed ----------------
//        if (!empty($filters['movementIdsAllowed'])) {
//            $allowedIds = array_unique((array)$filters['movementIdsAllowed']);
//
//            $qb->innerJoin('wvv.wodVersionVariantMovements', 'wvveAllowed')
//                ->innerJoin('wvveAllowed.movement', 'eAllowed')
//                ->andWhere($qb->expr()->in('eAllowed.id', ':allowedIds'))
//                ->setParameter('allowedIds', $allowedIds)
//                ->groupBy("$alias.id")
//                ->having($qb->expr()->eq('COUNT(DISTINCT eAllowed.id)', count($allowedIds)));
//        }

//        // ---------------- Movements allowed (strict) ----------------
//        if (!empty($filters['movementIdsAllowed'])) {
//            $allowedIds = array_unique((array)$filters['movementIdsAllowed']);
//
//            // DQL : récupérer les variantes contenant au moins un des IDs autorisés
//            $qb->innerJoin('wvv.wodVersionVariantMovements', 'wvveAllowed')
//                ->innerJoin('wvveAllowed.movement', 'eAllowed')
//                ->andWhere($qb->expr()->in('eAllowed.id', ':allowedIds'))
//                ->setParameter('allowedIds', $allowedIds)
//                ->addSelect('w.id as wodId, wvv.id as variantId');
//
//            // Récupérer les résultats pour filtrer strictement en PHP
//            $results = $qb->getQuery()->getArrayResult();
//
//            dd($results);
//
//            $validWodIds = [];
//            foreach ($results as $row) {
//                $movements = $em->getRepository(WodVersionVariantMovement::class)
//                    ->findBy(['wodVersionVariant' => $row['variantId']]);
//                $movementIds = array_map(fn($e) => $e->getMovement()->getId(), $movements);
//                sort($movementIds);
//                $allowedSorted = $allowedIds;
//                sort($allowedSorted);
//
//                if ($movementIds === $allowedSorted) {
//                    $validWodIds[] = $row['wodId'];
//                }
//            }
//            var_dump($validWodIds);
//
//            if (!empty($validWodIds)) {
//                var_dump("O1");
//                $qb->andWhere($qb->expr()->in("$alias.id", ':validWodIds'))
//                    ->setParameter('validWodIds', $validWodIds);
//            } else {
//                var_dump("O2");
//                // Aucun WOD ne correspond
//                $qb->andWhere('1 = 0');
//            }
//        }
//
//        // ---------------- Movements disallowed ----------------
//        if (!empty($filters['movementIdsDisallowed'])) {
//            $disallowedIds = array_unique((array)$filters['movementIdsDisallowed']);
//
//            $subQb2 = $em->createQueryBuilder();
//            $subQb2->select('1')
//                ->from(WodVersionVariantMovement::class, 'wvveSub2')
//                ->innerJoin('wvveSub2.wodVersionVariant', 'wvvSub2')
//                ->innerJoin('wvvSub2.wodVersion', 'wvSub2')
//                ->innerJoin('wvSub2.wod', 'wSub2')
//                ->innerJoin('wvveSub2.movement', 'eSub2')
//                ->where('wSub2 = ' . $alias)
//                ->andWhere($subQb2->expr()->in('eSub2.id', ':disallowedIds'));
//
//            $qb->andWhere($qb->expr()->not($qb->expr()->exists($subQb2->getDQL())))
//                ->setParameter('disallowedIds', $disallowedIds);
//        }
    }

    protected function applyPostFilters(array $entities, array $filters): array
    {
        /** @var Wod $entity */
        foreach ($entities as $entity) {
            foreach ($entity->getWodVersions() as $version) {
                $criteria = Criteria::create();
                $expr     = Criteria::expr();

                $constraints = [];

                // Filter gender
                if (!empty($filters['gender'])) {
                    $genders = (array)$filters['gender'];
                    $genders = array_map(fn($v) => $v instanceof Gender ? $v->value : $v, $genders);

                    $constraints[] = $expr->in('gender', $genders);
                }

                // Filter timeCap min
                if (!empty($filters['timeCapMin'])) {
                    $constraints[] = $expr->gte('timeCap', (int)$filters['timeCapMin']);
                }

                // Filter timeCap max
                if (!empty($filters['timeCapMax'])) {
                    $constraints[] = $expr->lte('timeCap', (int)$filters['timeCapMax']);
                }

                // Filter rounds min
                if (!empty($filters['roundsMin'])) {
                    $constraints[] = $expr->gte('rounds', (int)$filters['roundsMin']);
                }

                // Filter rounds max
                if (!empty($filters['roundsMax'])) {
                    $constraints[] = $expr->lte('rounds', (int)$filters['roundsMax']);
                }

                // Combine
                if ($constraints) {
                    $criteria->where(call_user_func_array([$expr, 'andX'], $constraints));
                }

                // Apply
                $filteredWodVersionVariants = $version->getWodVersionVariants()->matching($criteria);
                $version->setWodVersionVariants($filteredWodVersionVariants);
            }
        }

        return $entities;
    }

    protected function getAlias(): string
    {
        return 'w';
    }

    protected function getSearchableFields(): array
    {
        return [
            // Champs de l'entité principale
            'title'                                      => 'like',
            'description'                                => 'like',

            // Champs des relations
            'wodVersions.wodVersionVariants.description' => 'like',
        ];
    }

    protected function getSortableFields(): array
    {
        return [
            'id',
            'title',
        ];
    }
}
