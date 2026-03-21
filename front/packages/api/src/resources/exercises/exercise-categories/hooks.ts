"use client";

import { useQuery } from "../../../hooks/useQuery";
import type { ExerciseCategory, ExerciseCategorySummary } from "./types";

export function useGetExerciseCategories(params: Record<string, string> = {}) {
    return useQuery<ExerciseCategorySummary[]>("/exercise-categories", params);
}

export function useGetExerciseCategory(id: string) {
    return useQuery<ExerciseCategory>(`/exercise-categories/${id}`);
}
