"use client"

import { useQuery } from "../../hooks/useQuery"
import { useMutation } from "../../hooks/useMutation"
import type { Plan, CreatePlanPayload, UpdatePlanPayload } from "./types"

export function useGetPlacePlans(placeId: string) {
  return useQuery<Plan[]>(`/places/${placeId}/formulas`)
}

export function useCreatePlan(placeId: string) {
  return useMutation<Plan, CreatePlanPayload>(`/places/${placeId}/formulas`, "POST")
}

export function useUpdatePlan(placeId: string, planId: string) {
  return useMutation<Plan, UpdatePlanPayload>(`/places/${placeId}/formulas/${planId}`, "PATCH")
}

export function useDeletePlan(placeId: string, planId: string) {
  return useMutation<void, void>(`/places/${placeId}/formulas/${planId}`, "DELETE")
}
