"use client"

import { useGetWod } from "@workspace/api"
import { useParams } from "next/navigation"
import { WodDetailView } from "./_components/wod-detail-view"

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const { data, isLoading, error, refetch } = useGetWod(id)

  return (
    <WodDetailView
      wod={data ?? null}
      isLoading={isLoading}
      error={error}
      onRetry={refetch}
    />
  )
}
