"use client";

import { useQuery } from "../../hooks/useQuery";
import type { ExerciseCategorySummary } from "./types";

export function useGetExerciseCategories(params: Record<string, string> = {}) {
    return useQuery<ExerciseCategorySummary[]>("/exercise-categories", params);
}
