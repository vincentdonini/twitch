"use client"

import { ResourceDetailPage } from "@/ui/components/resource-detail-page"
import { useGetExerciseCategory } from "@workspace/api"
import { useTranslations } from "next-intl"
import { useParams } from "next/navigation"

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const t = useTranslations("exercises")

  const { data, isLoading, error, refetch } = useGetExerciseCategory(id)

  return (
    <ResourceDetailPage
      item={data}
      isLoading={isLoading}
      error={error}
      onRetry={refetch}
      href="/exercise-categories"
      backLabel={t("back")}
      badgeLabel={t("title")}
      errorTitle={t("error_title")}
      errorDescription={t("error_description")}
    />
  )
}
