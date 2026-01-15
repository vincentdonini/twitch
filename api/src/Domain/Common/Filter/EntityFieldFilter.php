<?php

namespace App\Domain\Common\Filter;

use App\Infrastructure\Filters\FilterCollection;
use App\Infrastructure\Filters\FilterValue;

class EntityFieldFilter
{
    public function match(object $object, ?FilterCollection $filters, array $mapping): bool
    {
        if (!$filters) {
            return true;
        }

        foreach ($filters as $filter) {
            if (!$filter instanceof FilterValue) {
                continue;
            }

            $field = $filter->field;

            if (!array_key_exists($field, $mapping)) {
                continue;
            }

            $value = $mapping[$field]($object);

            if (!$this->matchOperator($value, $filter->operator, $filter->value)) {
                return false;
            }
        }

        return true;
    }

    public function matchOperator(mixed $left, Operator $operator, mixed $right): bool
    {
        $left  = $this->normalizeValue($left);
        $right = $this->normalizeValue($right);

        switch ($operator) {
            case Operator::EQ:
                return $left == $right;

            case Operator::NEQ:
                return $left != $right;

            case Operator::LT:
                return $left < $right;

            case Operator::LTE:
                return $left <= $right;

            case Operator::GT:
                return $left > $right;

            case Operator::GTE:
                return $left >= $right;

            case Operator::LIKE:
                return is_string($left) && is_string($right)
                    && str_contains($left, $right);

            case Operator::NOT_LIKE:
                return is_string($left) && is_string($right)
                    && !str_contains($left, $right);

            case Operator::IN:
                return is_array($right) && in_array($left, $right, true);

            case Operator::NOT_IN:
                return is_array($right) && !in_array($left, $right, true);

            case Operator::BETWEEN:
                if (!is_array($right) || count($right) < 2) {
                    return false;
                }
                return $left >= $right[0] && $left <= $right[1];

            default:
                return false;
        }
    }

    public function normalizeValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \UnitEnum) {
            return $value->value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        if (is_array($value)) {
            return array_map(fn($v) => $this->normalizeValue($v), $value);
        }

        return strtolower((string)$value);
    }
}
