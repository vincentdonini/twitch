"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { MuscleArea, MuscleAreaSummary } from "./types"

export function useGetMuscleAreas(params: Record<string, string> = {}) {
  return useQuery<MuscleAreaSummary[]>("/muscle-areas", params)
}

export function useGetMuscleArea(id: string) {
  return useQuery<MuscleArea>(`/muscle-areas/${id}`)
}
