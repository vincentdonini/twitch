"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { WodCategory } from "../types"

export function useGetWodCategories(params: Record<string, string> = {}) {
  return useQuery<WodCategory[]>("/wod-categories", params)
}
