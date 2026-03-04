<?php

namespace App\UI\Adapters\Http\Common;

use BackedEnum;
use DateTimeImmutable;
use InvalidArgumentException;

trait HttpPayloadParser
{
    protected function parseBoolean(string $field): ?bool
    {
        if (!array_key_exists($field, $this->payload)) {
            return null;
        }

        $rawValue = $this->payload[$field];

        if ($rawValue === null) {
            return null;
        }

        $value = filter_var(
            $rawValue,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if ($value === null) {
            throw new InvalidArgumentException(
                sprintf('Invalid boolean value for "%s".', $field)
            );
        }

        return $value;
    }

    protected function parseEnum(string $field, string $enumClass): ?BackedEnum
    {
        if (!array_key_exists($field, $this->payload)) {
            return null;
        }

        $value = $this->payload[$field];

        if ($value === null) {
            return null;
        }

        if (!is_subclass_of($enumClass, BackedEnum::class)) {
            throw new InvalidArgumentException(
                sprintf('"%s" is not a backed enum.', $enumClass)
            );
        }

        $enum = $enumClass::tryFrom($value);

        if ($enum === null) {
            throw new InvalidArgumentException(
                sprintf('Invalid value "%s" for enum "%s".', $value, $enumClass)
            );
        }

        return $enum;
    }

    protected function parseDateTimeImmutable(string $field, ?string $format = null): ?DateTimeImmutable
    {
        if (!isset($this->payload[$field])) {
            return null;
        }

        $rawValue = $this->payload[$field];

        if (
            $rawValue === null ||
            $rawValue === '' ||
            $rawValue === 'undefined' ||
            $rawValue === 'null'
        ) {
            return null;
        }

        if (!is_string($rawValue)) {
            throw new InvalidArgumentException(
                sprintf('Invalid datetime type for "%s": expected string, got "%s".', $field, gettype($rawValue))
            );
        }

        try {
            if ($format !== null) {
                $date = DateTimeImmutable::createFromFormat($format, $rawValue);

                $errors = DateTimeImmutable::getLastErrors();
                if ($date === false || $errors['warning_count'] > 0 || $errors['error_count'] > 0) {
                    throw new InvalidArgumentException(
                        sprintf(
                            'Invalid datetime format for "%s": expected format "%s", got "%s".',
                            $field,
                            $format,
                            $rawValue
                        )
                    );
                }

                return $date;
            }

            return new DateTimeImmutable($rawValue);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(
                sprintf('Invalid datetime value for "%s": "%s".', $field, $rawValue),
                previous: $e
            );
        }
    }
}