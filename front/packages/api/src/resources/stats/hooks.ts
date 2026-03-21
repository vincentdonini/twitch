"use client"

import { useQuery } from "../../hooks/useQuery"
import type { AppStats } from "./types"

export function useGetStats() {
  return useQuery<AppStats>("/stats")
}
