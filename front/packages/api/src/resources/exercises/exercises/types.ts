import type { EquipmentSummary } from "../../equipments/types";
import type { ExerciseCategorySummary } from "../exercise-categories/types";

export type { EquipmentSummary };
export type { ExerciseCategorySummary };

export type Exercise = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string;
    wodCount: number;
};

export type ExerciseSummary = {
    id: string;
    slug: string;
    title: string;
    equipment: EquipmentSummary | null;
    exerciseCategory: ExerciseCategorySummary | null;
};

export type CreateExercisePayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateExercisePayload = CreateExercisePayload;
