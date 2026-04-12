"use client"

import { Card, CardContent } from "@workspace/ui/components/card"
import { useGetWods } from "@workspace/api"
import { Dumbbell } from "lucide-react"
import { useTranslations } from "next-intl"
import { AdminWodsTable } from "./admin-wods-table"

export function AdminWodsView() {
  const t = useTranslations("admin_wods")
  const { data, isLoading, error } = useGetWods()
  const wods = data ?? []

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

  return (
    <div className="flex flex-col gap-6">
      <Card className="border w-fit">
        <CardContent className="flex items-center gap-4">
          <Dumbbell className="size-8 text-muted-foreground" />
          <div>
            <p className="text-sm text-muted-foreground font-medium">{t("stat_total")}</p>
            <p className="text-2xl font-bold">{wods.length}</p>
          </div>
        </CardContent>
      </Card>

      <AdminWodsTable />
    </div>
  )
}
