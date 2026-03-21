export type ExerciseCategorySummary = {
    id: string;
    slug: string;
    title: string;
    wodCount?: number;
};

export type ExerciseCategory = {
    id: string;
    slug: string;
    title: string;
    summary: string;
    details: string;
    wodCount: number;
};
