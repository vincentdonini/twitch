"use client";

import {useQuery} from "../../hooks/useQuery";
import type {Exercise} from "./types";

export function useGetExercise(id: string) {
    return useQuery<Exercise>(`/exercises/${id}`);
}
