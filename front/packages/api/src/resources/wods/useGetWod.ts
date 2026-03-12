"use client"

import { useQuery } from "../../hooks/useQuery"
import type { WodDetail } from "./types"

export function useGetWod(id: string) {
  return useQuery<WodDetail>(`/wods/${id}`)
}
