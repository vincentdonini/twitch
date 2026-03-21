import type { ExerciseSummary } from "../exercises/exercises/types";

export type Benchmark = {
    id: string;
    slug: string;
    name: string;
    type: string;
    title: string;
    summary: string;
    exercise: ExerciseSummary;
};

export type CreateBenchmarkPayload = {
    slug: string;
    title: string;
    summary: string;
    details: string;
};

export type UpdateBenchmarkPayload = CreateBenchmarkPayload;
