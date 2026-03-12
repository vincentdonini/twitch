"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { MuscleArea } from "./types"

export function useGetMuscleAreas(params: Record<string, string> = {}) {
  return useQuery<MuscleArea[]>("/muscle-areas", params)
}
