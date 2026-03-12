"use client";

import {useQuery} from "../../hooks/useQuery";
import type {Achievement} from "./types";

export function useGetAchievement(id: string) {
    return useQuery<Achievement>(`/achievements/${id}`);
}
