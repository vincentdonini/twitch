"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { MuscleGroup } from "./types"

export function useGetMuscleGroups(params: Record<string, string> = {}) {
  return useQuery<MuscleGroup[]>("/muscle-groups", params)
}
