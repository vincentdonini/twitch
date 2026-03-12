"use client"

import { useParams } from "next/navigation"
import { useGetMuscle } from "@workspace/api"
import { useTranslations } from "next-intl"
import { ResourceDetailPage } from "@/ui/components/resource-detail-page"

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const t = useTranslations("muscles")

  const { data, isLoading, error, refetch } = useGetMuscle(id)

  return (
    <ResourceDetailPage
      item={data}
      isLoading={isLoading}
      error={error}
      onRetry={refetch}
      href="/muscles"
      backLabel={t("back")}
      badgeLabel={t("title")}
      errorTitle={t("error_title")}
      errorDescription={t("error_description")}
      relatedSections={[
        {
          heading: t("section_group"),
          items: data?.group ? [{ ...data.group, href: `/muscle-groups/${data.group.id}` }] : [],
        },
        {
          heading: t("section_area"),
          items: data?.area ? [{ ...data.area, href: `/muscle-areas/${data.area.id}` }] : [],
        },
      ]}
    />
  )
}
