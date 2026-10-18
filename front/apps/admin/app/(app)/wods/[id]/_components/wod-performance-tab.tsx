"use client"

import { WodLogScoreDrawer } from "@/app/(app)/wods/[id]/_components/wod-log-score-drawer"
import { PerformanceTab } from "@/components/performance-tab"
import { useGetMyWodScores, type WodDetail } from "@workspace/api"

interface WodPerformanceTabProps {
  wod: WodDetail
  onScoreLogged?: () => void
}

export function WodPerformanceTab({ wod, onScoreLogged }: WodPerformanceTabProps) {
  const { data: scores, isLoading, error, refetch } = useGetMyWodScores({
    "filters[wod.id][eq]": wod.id,
    sort: "performedAt",
    limit: "50",
  })

  const metrics = wod.type.allowedMetrics

  function handleScoreLogged() {
    void refetch()
    onScoreLogged?.()
  }

  return (
    <PerformanceTab
      scores={scores}
      isLoading={isLoading}
      error={error}
      metrics={metrics}
      onScoreLogged={handleScoreLogged}
      renderLogDrawer={(onSuccess, fullWidth) => (
        <WodLogScoreDrawer wod={wod} onSuccess={onSuccess} fullWidth={fullWidth} />
      )}
    />
  )
}
