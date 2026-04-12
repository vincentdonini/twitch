import type { User } from "../users/types"
import type { Plan } from "../plans/types"

export const SubscriptionStatus = {
  Active: "ACTIVE",
  Pending: "PENDING",
  Cancelled: "CANCELLED",
  Expired: "EXPIRED",
} as const
export type SubscriptionStatus = typeof SubscriptionStatus[keyof typeof SubscriptionStatus]

export type Subscription = {
  id: string
  user: User
  place: { id: string; name: string }
  formula: Pick<Plan, "id" | "title" | "type" | "price" | "currency" | "billingPeriod">
  status: SubscriptionStatus
  startedAt: string
  endedAt: string | null
  nextBillingAt: string | null
  cancelRequestedAt: string | null
  price: number
  currency: string
  sessionsUsedInCurrentPeriod: number
  createdAt: string
  updatedAt: string | null
}

export type CreateSubscriptionPayload = {
  userId: string
  started_at?: string
}
