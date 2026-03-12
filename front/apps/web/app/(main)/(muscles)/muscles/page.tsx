"use client"

import { useState } from "react"
import { useGetMuscles } from "@workspace/api"
import { useTranslations } from "next-intl"
import { ResourceListPage } from "@/ui/components/resource-list-page"

const LIMIT = 6

export default function Page() {
  const [page, setPage] = useState(1)
  const t = useTranslations("muscles")

  const { data, isLoading, error, pagination, refetch } = useGetMuscles({ page: String(page), limit: String(LIMIT) })

  return (
    <ResourceListPage
      data={data}
      isLoading={isLoading}
      error={error}
      pagination={pagination}
      onPageChange={setPage}
      onRetry={refetch}
      getHref={(id) => `/muscles/${id}`}
      title={t("title")}
      description={t("description")}
      errorTitle={t("error_title")}
      errorDescription={t("error_description")}
    />
  )
}
