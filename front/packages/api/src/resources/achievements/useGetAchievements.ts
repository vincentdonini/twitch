"use client";

import {useQuery} from "../../hooks/useQuery";
import type {Achievement} from "./types";

export function useGetAchievements(params: Record<string, string> = {}) {
    return useQuery<Achievement[]>("/achievements", params);
}
