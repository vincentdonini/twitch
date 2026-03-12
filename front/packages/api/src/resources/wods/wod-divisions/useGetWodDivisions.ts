"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { WodDivision } from "../types"

export function useGetWodDivisions(params: Record<string, string> = {}) {
  return useQuery<WodDivision[]>("/wod-divisions", params)
}
