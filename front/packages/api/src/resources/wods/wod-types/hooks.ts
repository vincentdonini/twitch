"use client"

import { useQuery } from "../../../hooks/useQuery"
import type { WodType } from "../types"

export function useGetWodTypes(params: Record<string, string> = {}) {
  return useQuery<WodType[]>("/wod-types", params)
}
