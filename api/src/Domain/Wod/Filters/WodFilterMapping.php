<?php

namespace App\Domain\Wod\Filters;

final class WodFilterMapping
{
    public const FIELD_MAP = [
        'name'          => 'name',
        'teamSize'      => 'teamSize',
        'type.id'       => 'wodType.id',
        'type.slug'     => 'wodType.slug',
        'category.id'   => 'wodCategory.id',
        'category.slug' => 'wodCategory.slug',
        'division.id'   => 'wodVariants.wodDivision.id',
        'division.slug' => 'wodVariants.wodDivision.slug',
        'gender'        => 'wodVariants.gender',
        'rounds'        => 'wodVariants.rounds',
        'timeCap'       => 'wodVariants.timeCap',
        'exercise.id'          => 'wodVariants.wodVariantExercises.exercise.id',
        'exerciseCategory.id'  => 'wodVariants.wodVariantExercises.exercise.exerciseCategory.id',
        'equipment.id'         => 'wodVariants.wodVariantExercises.exercise.equipment.id',
        'muscle.id'         => 'wodVariants.wodVariantExercises.exercise.muscles.id',
        'muscleGroup.id'    => 'wodVariants.wodVariantExercises.exercise.muscles.muscleGroup.id',
        'muscleArea.id'     => 'wodVariants.wodVariantExercises.exercise.muscles.muscleGroup.muscleArea.id',
        'muscleSegment.id'  => 'wodVariants.wodVariantExercises.exercise.muscleSegments.id',
    ];
}
