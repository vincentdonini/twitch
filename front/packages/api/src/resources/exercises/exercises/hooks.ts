"use client";

import { useQuery } from "../../../hooks/useQuery";
import type { Exercise, ExerciseSummary } from "./types";

export function useGetExercises(params: Record<string, string | string[]> = {}) {
    return useQuery<ExerciseSummary[]>("/exercises", params);
}

export function useGetExercise(id: string) {
    return useQuery<Exercise>(`/exercises/${id}`);
}
