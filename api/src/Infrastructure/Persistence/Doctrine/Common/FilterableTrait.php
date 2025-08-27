<?php

namespace App\Infrastructure\Persistence\Doctrine\Common;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;

trait FilterableTrait
{
    abstract protected function getAlias(): string;

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
        $this->applySearchFilters($qb, $alias, $search, $this->getSearchableFields());
        $this->applyCustomFilters($qb, $alias, $filters);

        $result = $qb->getQuery()->getResult();

        return $this->applyPostFilters($result, $filters);
    }

    public function countByFilters(
        ?string $search,
        array   $filters = [],
    ): int {
        $alias = $this->getAlias();
        $qb    = $this->createQueryBuilder($alias);

        $this->applySearchFilters($qb, $alias, $search, $this->getSearchableFields());
        $this->applyCustomFilters($qb, $alias, $filters);

        return count($qb->getQuery()->getResult());
    }

    protected function applySearchFilters(QueryBuilder $qb, string $alias, ?string $search, array $searchableFields): void
    {
        if (!$search) {
            return;
        }

        $orX = $qb->expr()->orX();
        $paramIndex = 0;

        foreach ($searchableFields as $fieldPath => $operator) {
            $parts = explode('.', $fieldPath);
            $currentAlias = $alias;

            // On fait les joins pour toutes les parties sauf la dernière (qui est le champ)
            for ($i = 0; $i < count($parts) - 1; $i++) {
                $joinAlias = $parts[$i];
                $relation = $parts[$i];

                // Si le join n’existe pas encore
                if (!in_array($joinAlias, array_keys($qb->getAllAliases()))) {
                    $qb->leftJoin("$currentAlias.$relation", $joinAlias);
                }
                $currentAlias = $joinAlias;
            }

            $fieldName = end($parts);
            $paramName = 'search' . $paramIndex;

            if ($operator === 'like') {
                $orX->add($qb->expr()->like("$currentAlias.$fieldName", ':' . $paramName));
            } elseif ($operator === 'exact') {
                $orX->add($qb->expr()->eq("$currentAlias.$fieldName", ':' . $paramName));
            }

            $qb->setParameter($paramName, $operator === 'like' ? '%' . $search . '%' : $search);
            $paramIndex++;
        }

        $qb->andWhere($orX);
    }


    /**
     * Parcourt récursivement les relations et ajoute les champs searchable
     */
    private function addSearchableFieldsRecursively(
        QueryBuilder $qb,
        string $alias,
        string $search,
        ClassMetadata $metadata,
        $orX,
        array &$visitedAliases
    ): void {
        // Convention : tableau protected $searchableFields dans chaque entité
        if (property_exists($metadata->getName(), 'searchableFields')) {
            $fields = $metadata->getName()::$searchableFields;
            foreach ($fields as $field => $operator) {
                if ($operator === 'like') {
                    $orX->add($qb->expr()->like("$alias.$field", ':search'));
                } elseif ($operator === 'exact') {
                    $orX->add($qb->expr()->eq("$alias.$field", ':search'));
                }
            }
        }

        // Parcours des associations
        foreach ($metadata->associationMappings as $assoc) {
            $assocAlias = $assoc['fieldName'];
            if (isset($visitedAliases[$assocAlias])) continue; // éviter les doublons

            $qb->leftJoin("$alias.{$assoc['fieldName']}", $assocAlias);
            $visitedAliases[$assocAlias] = true;

            $assocMetadata = $qb->getEntityManager()->getClassMetadata($assoc['targetEntity']);
            $this->addSearchableFieldsRecursively($qb, $assocAlias, $search, $assocMetadata, $orX, $visitedAliases);
        }
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

    protected function applyCustomFilters(QueryBuilder $qb, string $alias, array $filters): void
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

            $value     = $filters[$key];
            $field     = $conf['field'];
            $operator  = strtoupper($conf['operator'] ?? '=');
            $converter = $conf['converter'] ?? null;

            // Skip si valeur vide
            if ($value === '' || $value === null) {
                continue;
            }

            // Normalisation / conversion éventuelle
            if ($operator === 'IN') {
                // Toujours tableau
                $value = is_array($value) ? $value : [$value];

                // Skip si tableau vide → évite le IN()
                if (empty($value)) {
                    continue;
                }

                if ($converter) {
                    $value = array_map($converter, $value);
                }

                $qb->andWhere($qb->expr()->in($field, ":$key"))
                    ->setParameter($key, $value);

            } else {
                // Conversion simple (pour les bornes numériques, enums, etc.)
                if ($converter) {
                    $value = $converter($value);
                }

                $qb->andWhere("$field $operator :$key")
                    ->setParameter($key, $value);
            }
        }
    }
}
