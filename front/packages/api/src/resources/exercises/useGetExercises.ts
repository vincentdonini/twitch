"use client";

import {useQuery} from "../../hooks/useQuery";
import type {Exercise} from "./types";

export function useGetExercises(params: Record<string, string> = {}) {
    return useQuery<Exercise[]>("/exercises", params);
}
