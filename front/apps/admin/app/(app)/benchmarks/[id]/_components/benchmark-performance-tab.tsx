"use client"

import { type BenchmarkDetail, useGetMyBenchmarkScores } from "@workspace/api"
import { PerformanceTab } from "@/components/performance-tab"
import { resolveMetricFromType } from "@workspace/api"
import { BenchmarkLogScoreDrawer } from "./benchmark-log-score-drawer"

interface BenchmarkPerformanceTabProps {
  benchmark: BenchmarkDetail
  onScoreLogged?: () => void
}

export function BenchmarkPerformanceTab({ benchmark, onScoreLogged }: BenchmarkPerformanceTabProps) {
  const { data: scores, isLoading, error, refetch } = useGetMyBenchmarkScores({
    "filters[benchmark.id][eq]": benchmark.id,
    sort: "-performedAt",
    limit: "50",
  })

  const metric = resolveMetricFromType(benchmark.type)

  function handleScoreLogged() {
    void refetch()
    onScoreLogged?.()
  }

  return (
    <PerformanceTab
      scores={scores}
      isLoading={isLoading}
      error={error}
      metrics={[metric]}
      onScoreLogged={handleScoreLogged}
      renderLogDrawer={(onSuccess, fullWidth) => (
        <BenchmarkLogScoreDrawer benchmark={benchmark} onSuccess={onSuccess} fullWidth={fullWidth} />
      )}
    />
  )
}
