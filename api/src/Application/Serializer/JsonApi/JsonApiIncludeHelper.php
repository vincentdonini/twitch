<?php

namespace App\Application\Serializer\JsonApi;

class JsonApiIncludeHelper
{
    /**
     * Transforme une chaîne de type "wodTypes,wodVersions.variants" en arbre.
     */
    public static function parse(array $includes = []): array
    {
        $tree = [];

        foreach ($includes as $include) {
            if ($include === '') {
                continue;
            }

            // Explode par '.' pour gérer la hiérarchie
            $parts = explode('.', $include);
            $node  = &$tree;

            foreach ($parts as $part) {
                if (!isset($node[$part])) {
                    $node[$part] = [];
                }
                $node = &$node[$part];
            }
        }

        return $tree;
    }

    /**
     * Vérifie si une relation est demandée dans l'arbre.
     */
    public static function has(array $tree, string $relation): bool
    {
        return array_key_exists($relation, $tree);
    }

    /**
     * Récupère le sous-arbre d'une relation, ou tableau vide si non existant.
     */
    public static function subtree(array $tree, string $relation): array
    {
        return $tree[$relation] ?? [];
    }

    /**
     * Ajoute un DTO à l'includedCollector si la relation est demandée.
     */
    public static function addIfIncluded(
        string $relation,
        array  $tree,
        IncludedCollector $collector,
        callable $dtoFactory,
        array $groups = ['default']
    ): void {
        if (self::has($tree, $relation)) {
            $collector->add($relation, $dtoFactory(), $groups);
        }
    }
}
