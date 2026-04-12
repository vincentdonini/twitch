"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { MuscleSegment, MuscleSegmentSummary } from "./types"

export function useGetMuscleSegments(params: Record<string, string> = {}) {
  return useQuery<MuscleSegmentSummary[]>("/muscle-segments", params)
}

export function useGetMuscleSegment(id: string) {
  return useQuery<MuscleSegment>(`/muscle-segments/${id}`)
}
