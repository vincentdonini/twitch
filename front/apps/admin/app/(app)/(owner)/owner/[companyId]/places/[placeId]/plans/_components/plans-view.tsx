"use client"

import { Card, CardContent } from "@workspace/ui/components/card"
import { apiFetch, useGetPlacePlans, type Plan } from "@workspace/api"
import { Package } from "lucide-react"
import { useTranslations } from "next-intl"
import { PlansTable } from "./plans-table"

interface PlansViewProps {
  placeId: string
}

export function PlansView({ placeId }: PlansViewProps) {
  const t = useTranslations("plans")
  const { data, isLoading, error, refetch } = useGetPlacePlans(placeId)
  const plans = data ?? []

  if (isLoading) {
    return (
      <div className="flex items-center justify-center py-16 text-muted-foreground">
        {t("loading")}
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex items-center justify-center py-16 text-destructive">
        {t("error")}
      </div>
    )
  }

  async function handleDelete(plan: Plan) {
    try {
      await apiFetch(`/places/${placeId}/formulas/${plan.id}`, { method: "DELETE" })
    } finally {
      refetch()
    }
  }

  const activeCount = plans.filter((f) => f.status === "ACTIVE").length

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-start justify-between">
        <div className="grid grid-cols-2 gap-4">
          <Card className="border">
            <CardContent className="flex items-center gap-4">
              <Package className="size-8 text-muted-foreground" />
              <div>
                <p className="text-sm text-muted-foreground font-medium">{t("stat_total")}</p>
                <p className="text-2xl font-bold">{plans.length}</p>
              </div>
            </CardContent>
          </Card>
          <Card className="border">
            <CardContent className="flex items-center gap-4">
              <Package className="size-8 text-green-500" />
              <div>
                <p className="text-sm text-muted-foreground font-medium">{t("stat_active")}</p>
                <p className="text-2xl font-bold">{activeCount}</p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      <PlansTable placeId={placeId} plans={plans} onDelete={handleDelete} onCreated={refetch} onUpdated={refetch} />
    </div>
  )
}
