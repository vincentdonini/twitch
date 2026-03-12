"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { Muscle } from "./types"

export function useGetMuscle(id: string) {
  return useQuery<Muscle>(`/muscles/${id}`)
}
