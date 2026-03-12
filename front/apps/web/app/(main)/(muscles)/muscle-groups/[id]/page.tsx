"use client"

import { useParams } from "next/navigation"
import { useGetMuscleGroup } from "@workspace/api"
import { useTranslations } from "next-intl"
import { ResourceDetailPage } from "@/ui/components/resource-detail-page"

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const t = useTranslations("muscleGroups")

  const { data, isLoading, error, refetch } = useGetMuscleGroup(id)

  return (
    <ResourceDetailPage
      item={data}
      isLoading={isLoading}
      error={error}
      onRetry={refetch}
      href="/muscle-groups"
      backLabel={t("back")}
      badgeLabel={t("title")}
      errorTitle={t("error_title")}
      errorDescription={t("error_description")}
    />
  )
}
