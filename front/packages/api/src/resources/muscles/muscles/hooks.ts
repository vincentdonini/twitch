"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { Muscle, MuscleSummary } from "./types"

export function useGetMuscles(params: Record<string, string> = {}) {
  return useQuery<MuscleSummary[]>("/muscles", params)
}

export function useGetMuscle(id: string) {
  return useQuery<Muscle>(`/muscles/${id}`)
}
