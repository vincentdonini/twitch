<?php

namespace App\Shared\Api\Doctrine;

use Doctrine\ORM\QueryBuilder;

final class DoctrineFieldResolver
{
    private array $aliasMap = [];

    public function resolve(QueryBuilder $qb, string $field, string $rootAlias): string
    {
        $parts        = explode('.', $field);
        $currentAlias = $rootAlias;
        $path         = '';

        for ($i = 0; $i < count($parts) - 1; $i++) {
            $relation = $parts[$i];
            $path     .= $relation;

            if (!isset($this->aliasMap[$path])) {
                $alias                 = $relation . '_' . count($this->aliasMap);
                $this->aliasMap[$path] = $alias;
                $qb->leftJoin("$currentAlias.$relation", $alias);
            } else {
                $alias = $this->aliasMap[$path];
            }

            $currentAlias = $alias;
            $path         .= '.';
        }

        $finalField = end($parts);
        return "$currentAlias.$finalField";
    }
}
