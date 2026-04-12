"use client"

import { useQuery } from "../../hooks/useQuery"
import { useMutation } from "../../hooks/useMutation"
import type { Wod, WodDetail, AgeRange, LeaderboardEntry, CreateWodPayload, CreateWodScorePayload, UpdateWodPayload, CreateWodVariantPayload, UpdateWodVariantPayload, WodVariant } from "./types"

export function useGetWods(params: Record<string, string | string[]> = {}) {
  return useQuery<Wod[]>("/wods", params)
}

export function useGetWod(id: string, enabled = true) {
  return useQuery<WodDetail>(`/wods/${id}`, {}, true, enabled && !!id)
}

export function useCreateWod() {
  return useMutation<Wod, CreateWodPayload>("/wods", "POST")
}

export function useUpdateWod(id: string) {
  return useMutation<void, UpdateWodPayload>(`/wods/${id}`, "PATCH")
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

export function useCreateWodVariant(wodId: string) {
  return useMutation<WodVariant, CreateWodVariantPayload>(`/wods/${wodId}/variants`, "POST")
}

export function useUpdateWodVariant(variantId: string) {
  return useMutation<void, UpdateWodVariantPayload>(`/wod-variants/${variantId}`, "PATCH")
}

export function useDeleteWodVariant(variantId: string) {
  return useMutation<void, undefined>(`/wod-variants/${variantId}`, "DELETE")
}

export function useGetWodAgeRanges() {
  return useQuery<AgeRange[]>("/wod-age-ranges")
}
