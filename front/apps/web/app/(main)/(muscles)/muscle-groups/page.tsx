"use client"

import { useState } from "react"
import { useGetMuscleGroups } from "@workspace/api"
import { useTranslations } from "next-intl"
import { ResourceListPage } from "@/ui/components/resource-list-page"

const LIMIT = 6

export default function Page() {
  const [page, setPage] = useState(1)
  const t = useTranslations("muscleGroups")

  const { data, isLoading, error, pagination, refetch } = useGetMuscleGroups({ page: String(page), limit: String(LIMIT) })

  return (
    <ResourceListPage
      data={data}
      isLoading={isLoading}
      error={error}
      pagination={pagination}
      onPageChange={setPage}
      onRetry={refetch}
      getHref={(id) => `/muscle-groups/${id}`}
      title={t("title")}
      description={t("description")}
      errorTitle={t("error_title")}
      errorDescription={t("error_description")}
    />
  )
}
