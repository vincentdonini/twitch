<?php

namespace App\Domain\Equipment\Filters;

final class EquipmentFilterMapping
{
    public const FIELD_MAP = [
        'slug'    => 'slug',
        'title'   => 'contents.title',
        'summary' => 'contents.summary',
        'details' => 'contents.details',
    ];
}
