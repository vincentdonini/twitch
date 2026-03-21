"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { MuscleGroup, MuscleGroupSummary } from "./types"

export function useGetMuscleGroups(params: Record<string, string> = {}) {
  return useQuery<MuscleGroupSummary[]>("/muscle-groups", params)
}

export function useGetMuscleGroup(id: string) {
  return useQuery<MuscleGroup>(`/muscle-groups/${id}`)
}
