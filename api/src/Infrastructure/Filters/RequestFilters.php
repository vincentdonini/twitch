<?php

namespace App\Infrastructure\Filters;

use App\Domain\Common\Filter\Operator;
use BackedEnum;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use ValueError;

final class RequestFilters
{
    public static function extractValues(
        Request $request,
        array   $fieldMapping,
        array   $allowedOperators,
        array   $allowedFields
    ): FilterCollection {
        $rawFilters = $request->query->all()['filters'] ?? [];
        $filters    = [];

        foreach ($rawFilters as $frontField => $conditions) {
            if (!is_array($conditions)) {
                throw new InvalidArgumentException();
            }

            $field = $fieldMapping[$frontField] ?? $frontField;

            if (!\in_array($field, $allowedFields, true)) {
                throw new AccessDeniedHttpException(
                    sprintf("Filtering on field '%s' is not allowed for your role", $field)
                );
            }

            if (!isset($allowedOperators[$field])) {
                throw new InvalidArgumentException("Field '$field' is not filterable");
            }

            foreach ($conditions as $operatorStr => $value) {
                $operatorStr = strtolower($operatorStr);

                try {
                    $operatorEnum = Operator::from($operatorStr);
                } catch (ValueError) {
                    throw new InvalidArgumentException("Unknown operator '$operatorStr' for field '$field'");
                }

                $allowedOps = array_map(
                    fn($op) => $op instanceof BackedEnum ? $op->value : (string)$op,
                    $allowedOperators[$field]
                );

                if (!in_array($operatorEnum->value, $allowedOps, true)) {
                    throw new InvalidArgumentException(
                        sprintf(
                            "Operator '%s' not allowed for field '%s'. Allowed: %s",
                            $operatorEnum->value,
                            $field,
                            implode(', ', $allowedOps)
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
