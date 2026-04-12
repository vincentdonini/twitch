"use client"

import { useQuery } from "../../hooks/useQuery"
import { useMutation } from "../../hooks/useMutation"
import { Subscription, CreateSubscriptionPayload } from "./types"

export function useGetPlaceSubscriptions(placeId: string) {
  return useQuery<Subscription[]>(`/places/${placeId}/subscriptions`)
}

export function useGetPlanSubscriptions(placeId: string, planId: string, enabled = true) {
  return useQuery<Subscription[]>(`/places/${placeId}/formulas/${planId}/subscriptions`, {}, true, enabled)
}

export function useCreateSubscription(placeId: string, planId: string) {
  return useMutation<Subscription, CreateSubscriptionPayload>(
    `/places/${placeId}/formulas/${planId}/subscriptions`,
    "POST"
  )
}
