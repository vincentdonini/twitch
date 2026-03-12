"use client"

import { useParams } from "next/navigation"
import { useGetEquipment } from "@workspace/api"
import { useTranslations } from "next-intl"
import { ResourceDetailPage } from "@/ui/components/resource-detail-page"

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const t = useTranslations("equipments")

  const { data, isLoading, error, refetch } = useGetEquipment(id)

  return (
    <ResourceDetailPage
      item={data}
      isLoading={isLoading}
      error={error}
      onRetry={refetch}
      href="/equipments"
      backLabel={t("back")}
      errorTitle={t("error_title")}
      errorDescription={t("error_description")}
    />
  )
}
