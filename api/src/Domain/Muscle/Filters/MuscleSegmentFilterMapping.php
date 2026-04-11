<?php

namespace App\Domain\Muscle\Filters;

final class MuscleSegmentFilterMapping
{
    public const FIELD_MAP = [
        'slug'      => 'slug',
        'muscle.id' => 'muscle.id',
        'title'     => 'contents.title',
        'summary'   => 'contents.summary',
        'details'   => 'contents.details',
    ];
}
