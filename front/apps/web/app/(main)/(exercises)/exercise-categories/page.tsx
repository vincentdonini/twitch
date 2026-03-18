"use client"

import { ResourceListPage } from "@/ui/components/resource-list-page"
import { useGetExercises } from "@workspace/api"
import { useTranslations } from "next-intl"
import { useState } from "react"

const LIMIT = 6

export default function Page() {
  const [page, setPage] = useState(1)
  const t = useTranslations("exercise_categories")

  const { data, isLoading, error, pagination, refetch } = useGetExercises({ page: String(page), limit: String(LIMIT) })

  return (
    <ResourceListPage
      data={data}
      isLoading={isLoading}
      error={error}
      pagination={pagination}
      onPageChange={setPage}
      onRetry={refetch}
      getHref={(id) => `/exercise-categories/${id}`}
      title={t("title")}
      description={t("description")}
      errorTitle={t("error_title")}
      errorDescription={t("error_description")}
    />
  )
}
