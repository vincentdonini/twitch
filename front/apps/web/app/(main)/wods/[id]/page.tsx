"use client"

import { WodDetailPage } from "@/app/(main)/wods/[id]/_components/wod-detail-page"
import { useGetWod } from "@workspace/api"
import { useParams } from "next/navigation"

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const { data, isLoading, error, refetch } = useGetWod(id)

  return (
    <WodDetailPage
      item={data}
      isLoading={isLoading}
      error={error}
      onRetry={refetch}
    />
  )
}
