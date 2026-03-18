export type Exercise = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string;
    wodCount: number;
};

export type EquipmentSummary = {
    id: string;
    slug: string;
    title: string;
};

export type ExerciseCategorySummary = {
    id: string;
    slug: string;
    title: string;
    wodCount?: number;
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
