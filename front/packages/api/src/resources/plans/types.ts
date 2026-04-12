export const PlanStatus = {
  Active: "ACTIVE",
  Inactive: "INACTIVE",
  Archived: "ARCHIVED",
} as const
export type PlanStatus = typeof PlanStatus[keyof typeof PlanStatus]

export const PlanType = {
  Subscription: "SUBSCRIPTION",
  Pack: "PACK",
  DropIn: "DROP_IN",
} as const
export type PlanType = typeof PlanType[keyof typeof PlanType]

export const PlanBillingPeriod = {
  Weekly: "WEEKLY",
  Monthly: "MONTHLY",
  Yearly: "YEARLY",
} as const
export type PlanBillingPeriod = typeof PlanBillingPeriod[keyof typeof PlanBillingPeriod]

export const PlanCurrency = {
  Eur: "EUR",
} as const
export type PlanCurrency = typeof PlanCurrency[keyof typeof PlanCurrency]

export type Plan = {
  id: string
  place: { id: string; name: string }
  title: string
  description: string | null
  isPublic: boolean
  status: PlanStatus
  price: number
  currency: PlanCurrency
  type: PlanType
  billingPeriod: PlanBillingPeriod | null
  engagementDurationInMonths: number | null
  cancellationNoticeInDays: number | null
  maxSessionsPerDay: number | null
  maxSessionsPerWeek: number | null
  maxSessionsPerMonth: number | null
  totalSessions: number | null
  validityInDays: number | null
  minAge: number | null
  maxAge: number | null
  createdAt: string
  updatedAt: string | null
}

export type UpdatePlanPayload = Partial<CreatePlanPayload>

export type CreatePlanPayload = {
  title: string
  description?: string | null
  isPublic: boolean
  status: PlanStatus
  price: number
  currency?: PlanCurrency
  type: PlanType
  billingPeriod?: PlanBillingPeriod | null
  engagementDurationInMonths?: number | null
  cancellationNoticeInDays?: number | null
  maxSessionsPerDay?: number | null
  maxSessionsPerWeek?: number | null
  maxSessionsPerMonth?: number | null
  totalSessions?: number | null
  validityInDays?: number | null
  minAge?: number | null
  maxAge?: number | null
}
