"use client"

import { useGetMuscleSegments } from "@workspace/api"
import { useTranslations } from "next-intl"
import { useEffect } from "react"
import type { OptionState } from "./types"
import { WodSearchableFilter } from "./wod-searchable-filter"

interface WodMuscleSegmentFilterProps {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function WodMuscleSegmentFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: WodMuscleSegmentFilterProps) {
  const t = useTranslations("common.wods")
  const { data: muscleSegments, isLoading } = useGetMuscleSegments({ limit: "500", sort: "title" })

  useEffect(() => {
    if (!muscleSegments?.length) return
    const discovered: Record<string, string> = {}
    muscleSegments.forEach(s => { discovered[s.id] = s.title })
    onLabelsDiscovered?.(discovered)
  }, [muscleSegments]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <WodSearchableFilter
      items={muscleSegments ?? []}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("muscle_segment_search_placeholder")}
      noResultsText={t("muscle_segment_no_results")}
    />
  )
}
