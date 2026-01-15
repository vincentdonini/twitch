<?php

namespace App\Infrastructure\Doctrine\Repository\Common;

use Doctrine\ORM\QueryBuilder;

trait FilterableTrait
{
    abstract protected function getAlias(): string;

    // TODO : To remove
    abstract protected function getSearchableFields(): array;

    abstract protected function getSortableFields(): array;

    abstract protected function createQueryBuilder(string $alias): QueryBuilder;

    public function findByFilters(
        ?string $sortBy,
        ?string $sortOrder,
        int     $offset = 0,
        int     $limit = 10,
        ?string $search = null,
        array   $filters = [],
    ): array {
        $alias = $this->getAlias();
        $qb = $this->createQueryBuilder($alias);

        $this->applySorting($qb, $alias, $sortBy, $sortOrder, $this->getSortableFields());
        $this->applyPagination($qb, $offset, $limit);
        $this->applyFilters($qb, $alias, $filters);

        $result = $qb->getQuery()->getResult();

        return $this->applyPostFilters($result, $filters);
    }

    public function countByFilters(
        ?string $search,
        array   $filters = [],
    ): int {
        $alias = $this->getAlias();
        $qb    = $this->createQueryBuilder($alias);

        $this->applyCustomFilters($qb, $alias, $filters);

        return count($qb->getQuery()->getResult());
    }

    // TODO : To remove
    protected function applySearchFilters(QueryBuilder $qb, string $alias, ?string $search, array $searchableFields): void
    {

    }

    protected function applySorting(QueryBuilder $qb, string $alias, ?string $sortBy, ?string $sortOrder, array $validSortFields): void
    {
        $order = strtoupper($sortOrder ?? 'ASC');
        $order = in_array($order, ['ASC', 'DESC']) ? $order : 'ASC';

        if ($sortBy && in_array($sortBy, $validSortFields, true)) {
            $qb->orderBy("$alias.$sortBy", $order);
        } else {
            $qb->orderBy("$alias.id", 'ASC');
        }
    }

    protected function applyPagination(QueryBuilder $qb, int $offset, int $limit): void
    {
        if ($limit != -1) {
            $qb->setFirstResult($offset)
                ->setMaxResults($limit);
        }
    }

    protected function applyFilters(QueryBuilder $qb, string $alias, array $filters): void
    {

    }

    protected function applyPostFilters(iterable $entities, array $filters): iterable
    {
        return $entities;
    }

    protected function applyFiltersToQueryBuilder(QueryBuilder $qb, array $filters, array $config): void
    {
        foreach ($config as $key => $conf) {
            if (!array_key_exists($key, $filters)) {
                continue;
            }

            $value = $filters[$key];

            // Skip si valeur vide
            if ($value === '' || $value === null) {
                continue;
            }

            // Jointure automatique si définie
            if (isset($conf['join'])) {
                $joinAlias = $conf['join']['alias'];
                $relation  = $conf['join']['relation'];

                // Vérifie si le join existe déjà
                $existingJoins = array_map(fn($j) => $j->getAlias(), $qb->getDQLPart('join')[$alias] ?? []);
                if (!in_array($joinAlias, $existingJoins, true)) {
                    $qb->leftJoin("$alias.$relation", $joinAlias);
                }
            }

            // Conversion éventuelle
            if (isset($conf['converter']) && is_callable($conf['converter'])) {
                $value = $conf['converter']($value);
            }

            // Application du filtre
            $operator = strtoupper($conf['operator'] ?? '=');
            $field    = $conf['field'];

            if ($operator === 'IN') {
                $value = is_array($value) ? $value : [$value];
                if (!empty($value)) {
                    $qb->andWhere($qb->expr()->in($field, ":$key"))
                        ->setParameter($key, $value);
                }
            } else {
                $qb->andWhere("$field $operator :$key")
                    ->setParameter($key, $value);
            }
        }
        dd("here");
    }
}
