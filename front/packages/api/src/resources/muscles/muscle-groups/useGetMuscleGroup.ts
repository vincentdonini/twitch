"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { MuscleGroup } from "./types"

export function useGetMuscleGroup(id: string) {
  return useQuery<MuscleGroup>(`/muscle-groups/${id}`)
}
