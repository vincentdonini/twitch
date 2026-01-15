<?php

namespace App\Infrastructure\Filters;

use App\Domain\Common\Filter\Operator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use ValueError;

final class RequestFilters
{
    public static function extractValues(
        Request $request,
        array   $fieldMapping,
        array   $allowedOperators
    ): FilterCollection {
        $rawFilters = $request->query->all()['filters'] ?? [];
        $filters    = [];

        foreach ($rawFilters as $frontField => $conditions) {
            if (!is_array($conditions)) {
                throw new InvalidArgumentException("Filters for field '$frontField' must be an array");
            }

            $field = $fieldMapping[$frontField] ?? $frontField;

            foreach ($conditions as $operatorStr => $value) {
                $operatorStr = strtolower($operatorStr);

                try {
                    $operatorEnum = Operator::from($operatorStr);
                } catch (ValueError) {
                    throw new InvalidArgumentException("Unknown operator '$operatorStr' for field '$field'");
                }

                if (!isset($allowedOperators[$field])) {
                    throw new InvalidArgumentException("Field '$field' is not filterable");
                }
                $allowed = array_map(
                    fn($op) => $op instanceof \BackedEnum ? $op->value : (string)$op,
                    $allowedOperators[$field]
                );

                if (!in_array($operatorEnum->value, $allowed, true)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            "Operator '%s' not allowed for field '%s'. Allowed: %s",
                            $operatorEnum->value,
                            $field,
                            implode(', ', $allowed)
                        )
                    );
                }

                $filters[] = new FilterValue(
                    field   : $field,
                    operator: $operatorEnum,
                    value   : $value,
                );
            }
        }

        return new FilterCollection($filters);
    }

    public static function hasFilter(FilterCollection $filters, string $field): bool
    {
        foreach ($filters->all() as $filter) {
            if ($filter->field === $field) {
                return true;
            }
        }
        return false;
    }

    public static function getFilter(FilterCollection $filters, string $field): ?FilterValue
    {
        foreach ($filters->all() as $filter) {
            if ($filter->field === $field) {
                return $filter;
            }
        }
        return null;
    }

    public static function getFiltersForField(FilterCollection $filters, string $field): array
    {
        return array_filter(
            $filters->all(),
            fn($filter) => $filter->field === $field
        );
    }

    public static function hasOperator(FilterCollection $filters, string $field, Operator $operator): bool
    {
        foreach ($filters->all() as $filter) {
            if ($filter->field === $field && $filter->operator === $operator) {
                return true;
            }
        }
        return false;
    }
}
