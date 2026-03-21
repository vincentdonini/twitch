"use client"

import { useQuery } from "../../hooks/useQuery"
import { useMutation } from "../../hooks/useMutation"
import type { Wod, WodDetail, LeaderboardEntry, CreateWodScorePayload } from "./types"

export function useGetWods(params: Record<string, string | string[]> = {}) {
  return useQuery<Wod[]>("/wods", params)
}

export function useGetWod(id: string) {
  return useQuery<WodDetail>(`/wods/${id}`)
}

export function useGetWodLeaderboard(
  wodId: string,
  divisionId: string,
  gender: "male" | "female" | "mixed",
) {
  return useQuery<LeaderboardEntry[]>(
    `/wods/${wodId}/division/${divisionId}/leaderboard/${gender}`,
  )
}

export function useCreateWodScore() {
  return useMutation<{ id: string }, CreateWodScorePayload>("/wod-scores", "POST")
}
