"use client"

import { useGetBenchmark } from "@workspace/api"
import { useParams } from "next/navigation"
import { BenchmarkDetailView } from "./_components/benchmark-detail-view"

export default function Page() {
  const { id } = useParams<{ id: string }>()
  const { data, isLoading, error, refetch } = useGetBenchmark(id)

  return (
    <BenchmarkDetailView
      benchmark={data ?? null}
      isLoading={isLoading}
      error={error}
      onRetry={refetch}
    />
  )
}
