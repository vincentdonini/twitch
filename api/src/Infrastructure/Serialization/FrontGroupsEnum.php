<?php

namespace App\Infrastructure\Serialization;

final class FrontGroupsEnum
{
    public const ADMIN = 'front:admin';

    // ACHIEVEMENTS
    // -----------------------------------------------------------------------------------------------------------------
    public const ACHIEVEMENT_COMPARE = 'front:achievement:compare';
    public const ACHIEVEMENT_CREATE = 'front:achievement:create';
    public const ACHIEVEMENT_MANAGE = 'front:achievement:manage';
    public const ACHIEVEMENT_LIST = 'front:achievement:list';
    public const ACHIEVEMENT_DETAIL = 'front:achievement:detail';
    public const ACHIEVEMENT_GROUP_MANAGE = 'front:achievementGroup:manage';
    public const ACHIEVEMENT_GROUP_LIST = 'front:achievementGroup:list';
    public const ACHIEVEMENT_GROUP_DETAIL = 'front:achievementGroup:detail';
    public const ACHIEVEMENT_CATEGORY_MANAGE = 'front:achievementCategory:manage';
    public const ACHIEVEMENT_CATEGORY_LIST   = 'front:achievementCategory:list';
    public const ACHIEVEMENT_CATEGORY_DETAIL = 'front:achievementCategory:detail';


    // ATHLETES
    // -----------------------------------------------------------------------------------------------------------------
    public const ATHLETE_LIST = 'front:athletes:list';


    // BENCHMARK
    // -----------------------------------------------------------------------------------------------------------------
    public const BENCHMARK_CREATE = 'front:benchmark:create';
    public const BENCHMARK_MANAGE = 'front:benchmark:manage';
    public const BENCHMARK_LIST   = 'front:benchmark:list';
    public const BENCHMARK_DETAIL = 'front:benchmark:detail';
    public const BENCHMARK_SCORE_MANAGE = 'front:benchmarkScore:manage';
    public const BENCHMARK_SCORE_LIST   = 'front:benchmarkScore:list';
    public const BENCHMARK_SCORE_DETAIL = 'front:benchmarkScore:detail';


    // CONTENT
    // -----------------------------------------------------------------------------------------------------------------
    public const CONTENT_ACHIEVEMENT_MANAGE = 'front:contentAchievement:manage';
    public const CONTENT_ACHIEVEMENT_LIST = 'front:contentAchievement:list';
    public const CONTENT_ACHIEVEMENT_CATEGORY_LIST = 'front:contentAchievementCategory:list';
    public const CONTENT_ACHIEVEMENT_GROUP_LIST = 'front:contentAchievementGroup:list';
    public const CONTENT_ACHIEVEMENT_LEVEL_LIST = 'front:contentAchievementLevel:list';
    public const CONTENT_ACHIEVEMENT_CRITERIA_LIST = 'front:contentAchievementCriteria:list';

    public const CONTENT_BENCHMARK_MANAGE = 'front:contentBenchmark:manage';
    public const CONTENT_BENCHMARK_LIST   = 'front:contentBenchmark:list';

    public const CONTENT_EQUIPMENT_MANAGE = 'front:contentEquipment:manage';
    public const CONTENT_EQUIPMENT_LIST   = 'front:contentEquipment:list';

    public const CONTENT_EXERCISE_MANAGE = 'front:contentExercise:manage';
    public const CONTENT_EXERCISE_LIST   = 'front:contentExercise:list';

    public const CONTENT_EXERCISE_CATEGORY_MANAGE = 'front:contentExerciseCategory:manage';
    public const CONTENT_EXERCISE_CATEGORY_LIST   = 'front:contentExerciseCategory:list';

    public const CONTENT_MUSCLE_MANAGE = 'front:contentMuscle:manage';
    public const CONTENT_MUSCLE_LIST   = 'front:contentMuscle:list';

    public const CONTENT_MUSCLE_AREA_MANAGE = 'front:contentMuscleArea:manage';
    public const CONTENT_MUSCLE_AREA_LIST   = 'front:contentMuscleArea:list';

    public const CONTENT_MUSCLE_GROUP_MANAGE = 'front:contentMuscleGroup:manage';
    public const CONTENT_MUSCLE_GROUP_LIST   = 'front:contentMuscleGroup:list';

    public const CONTENT_WOD_MANAGE = 'front:contentWod:manage';
    public const CONTENT_WOD_LIST   = 'front:contentWod:list';

    public const CONTENT_WOD_AGE_RANGE_MANAGE = 'front:contentWodAgeRange:manage';
    public const CONTENT_WOD_AGE_RANGE_LIST   = 'front:contentWodAgeRange:list';

    public const CONTENT_WOD_CATEGORY_MANAGE = 'front:contentWodCategory:manage';
    public const CONTENT_WOD_CATEGORY_LIST   = 'front:contentWodCategory:list';

    public const CONTENT_WOD_DIVISION_MANAGE = 'front:contentWodDivision:manage';
    public const CONTENT_WOD_DIVISION_LIST   = 'front:contentWodDivision:list';

    public const CONTENT_WOD_TYPE_MANAGE = 'front:contentWodType:manage';
    public const CONTENT_WOD_TYPE_LIST   = 'front:contentWodType:list';


    // USER
    // -----------------------------------------------------------------------------------------------------------------
    public const USER_ME     = 'front:user:me';
    public const USER_MANAGE = 'front:user:manage';
    public const USER_LIST   = 'front:user:list';
    public const USER_DETAIL = 'front:user:detail';


    // EQUIPMENT
    // -----------------------------------------------------------------------------------------------------------------
    public const EQUIPMENT_MANAGE = 'front:equipment:manage';
    public const EQUIPMENT_LIST   = 'front:equipment:list';
    public const EQUIPMENT_DETAIL = 'front:equipment:detail';


    // EXERCISE
    // -----------------------------------------------------------------------------------------------------------------
    public const EXERCISE_MANAGE = 'front:exercise:manage';
    public const EXERCISE_LIST   = 'front:exercise:list';
    public const EXERCISE_DETAIL = 'front:exercise:detail';

    public const EXERCISE_CATEGORY_MANAGE = 'front:exerciseCategory:manage';
    public const EXERCISE_CATEGORY_LIST   = 'front:exerciseCategory:list';
    public const EXERCISE_CATEGORY_DETAIL = 'front:exerciseCategory:detail';


    // FORMULA
    // -----------------------------------------------------------------------------------------------------------------
    public const FORMULA_MANAGE = 'front:formula:manage';
    public const FORMULA_LIST   = 'front:formula:list';
    public const FORMULA_DETAIL = 'front:formula:detail';


    // SUBSCRIPTION
    // -----------------------------------------------------------------------------------------------------------------
    public const SUBSCRIPTION_MANAGE = 'front:subscription:manage';
    public const SUBSCRIPTION_LIST   = 'front:subscription:list';
    public const SUBSCRIPTION_DETAIL = 'front:subscription:detail';


    // MUSCLE
    // -----------------------------------------------------------------------------------------------------------------
    public const MUSCLE_MANAGE = 'front:muscle:manage';
    public const MUSCLE_LIST   = 'front:muscle:list';
    public const MUSCLE_DETAIL = 'front:muscle:detail';

    public const MUSCLE_GROUP_MANAGE = 'front:muscleGroup:manage';
    public const MUSCLE_GROUP_LIST   = 'front:muscleGroup:list';
    public const MUSCLE_GROUP_DETAIL = 'front:muscleGroup:detail';

    public const MUSCLE_AREA_MANAGE = 'front:muscleArea:manage';
    public const MUSCLE_AREA_LIST   = 'front:muscleArea:list';
    public const MUSCLE_AREA_DETAIL = 'front:muscleArea:detail';

    // ORGANIZATION
    // -----------------------------------------------------------------------------------------------------------------
    public const COMPANY_CREATE = 'front:company:create';
    public const COMPANY_MANAGE = 'front:company:manage';
    public const COMPANY_LIST_PUBLIC   = 'front:company:list:public';
    public const COMPANY_LIST_ADMIN   = 'front:company:list:admin';
    public const COMPANY_DETAIL_PUBLIC = 'front:company:detail:public';
    public const COMPANY_DETAIL_ADMIN = 'front:company:detail:admin';

    public const PLACE_CREATE       = 'front:place:create';
    public const PLACE_MANAGE       = 'front:place:manage';
    public const PLACE_LIST_PUBLIC  = 'front:place:list:public';
    public const PLACE_LIST_ADMIN   = 'front:place:list:admin';
    public const PLACE_DETAIL_PUBLIC = 'front:place:detail:public';
    public const PLACE_DETAIL_ADMIN  = 'front:place:detail:admin';


    // SECURITY
    // -----------------------------------------------------------------------------------------------------------------
    public const ROLE_MANAGE = 'front:role:manage';
    public const ROLE_LIST   = 'front:role:list';
    public const ROLE_DETAIL = 'front:role:detail';

    public const PERMISSION_MANAGE = 'front:permission:manage';
    public const PERMISSION_LIST   = 'front:permission:list';
    public const PERMISSION_DETAIL = 'front:permission:detail';

    public const ROLE_PERMISSION_MANAGE = 'front:rolePermission:manage';
    public const ROLE_PERMISSION_LIST   = 'front:rolePermission:list';
    public const ROLE_PERMISSION_DETAIL = 'front:rolePermission:detail';


    // WOD
    // -----------------------------------------------------------------------------------------------------------------
    public const WOD_CREATE = 'front:wod:create';
    public const WOD_MANAGE = 'front:wod:manage';
    public const WOD_LIST   = 'front:wod:list';
    public const WOD_DETAIL = 'front:wod:detail';

    public const WOD_AGE_RANGE_MANAGE = 'front:wodAgeRange:manage';
    public const WOD_AGE_RANGE_LIST   = 'front:wodAgeRange:list';
    public const WOD_AGE_RANGE_DETAIL = 'front:wodAgeRange:detail';

    public const WOD_CATEGORY_MANAGE = 'front:wodCategory:manage';
    public const WOD_CATEGORY_LIST   = 'front:wodCategory:list';
    public const WOD_CATEGORY_DETAIL = 'front:wodCategory:detail';

    public const WOD_DIVISION_MANAGE = 'front:wodDivision:manage';
    public const WOD_DIVISION_LIST   = 'front:wodDivision:list';
    public const WOD_DIVISION_DETAIL = 'front:wodDivision:detail';

    public const WOD_LEADERBOARD = 'front:wodLeaderboard';

    public const WOD_SCORE_MANAGE = 'front:wodScore:manage';
    public const WOD_SCORE_LIST   = 'front:wodScore:list';
    public const WOD_SCORE_DETAIL = 'front:wodScore:detail';

    public const WOD_TYPE_MANAGE = 'front:wodType:manage';
    public const WOD_TYPE_LIST   = 'front:wodType:list';
    public const WOD_TYPE_DETAIL = 'front:wodType:detail';

    public const WOD_VERSION_MANAGE = 'front:wodVersion:manage';
    public const WOD_VERSION_LIST   = 'front:wodVersion:list';
    public const WOD_VERSION_DETAIL = 'front:wodVersion:detail';

    public const WOD_VARIANT_MANAGE = 'front:wodVariant:manage';
    public const WOD_VARIANT_LIST   = 'front:wodVariant:list';
    public const WOD_VARIANT_DETAIL = 'front:wodVariant:detail';

    public const WOD_VARIANT_EXERCISE_MANAGE = 'front:wodVariantVersion:manage';
    public const WOD_VARIANT_EXERCISE_LIST   = 'front:wodVariantVersion:list';
    public const WOD_VARIANT_EXERCISE_DETAIL = 'front:wodVariantVersion:detail';
}
