"use client"

import { useQuery } from "../../hooks/useQuery"
import type { LeaderboardEntry } from "./types"

export function useGetWodLeaderboard(
  wodId: string,
  divisionId: string,
  gender: "male" | "female" | "mixed",
) {
  return useQuery<LeaderboardEntry[]>(
    `/wods/${wodId}/division/${divisionId}/leaderboard/${gender}`,
  )
}
