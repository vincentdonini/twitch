"use client"

import { useQuery } from "../../hooks/useQuery"
import type { Wod } from "./types"

export function useGetWods(params: Record<string, string> = {}) {
  return useQuery<Wod[]>("/wods", params)
}
