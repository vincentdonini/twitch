"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { MuscleArea } from "./types"

export function useGetMuscleArea(id: string) {
  return useQuery<MuscleArea>(`/muscle-areas/${id}`)
}
