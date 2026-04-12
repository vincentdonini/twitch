import type { EquipmentSummary } from "../../equipments/types";
import type { ExerciseCategorySummary } from "../exercise-categories/types";
import type { MuscleSummary } from "../../muscles/muscles/types";

export type Exercise = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string;
    wodCount: number;
    muscles: MuscleSummary[];
};

export type ExerciseSummary = {
    id: string;
    slug: string;
    title: string;
    titlePlural?: string | null;
    equipment: EquipmentSummary | null;
    exerciseCategory: ExerciseCategorySummary | null;
    muscles: MuscleSummary[];
};

export type CreateExercisePayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateExercisePayload = CreateExercisePayload;
