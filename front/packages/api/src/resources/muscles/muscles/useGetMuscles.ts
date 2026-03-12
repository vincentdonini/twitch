"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { Muscle } from "./types"

export function useGetMuscles(params: Record<string, string> = {}) {
  return useQuery<Muscle[]>("/muscles", params)
}
