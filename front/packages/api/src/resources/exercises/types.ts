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
};

export type CreateExercisePayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateExercisePayload = CreateExercisePayload;
