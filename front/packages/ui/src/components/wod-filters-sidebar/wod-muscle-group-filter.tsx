"use client"

import { useGetMuscleGroups } from "@workspace/api"
import { useTranslations } from "next-intl"
import { useEffect } from "react"
import type { OptionState } from "./types"
import { WodSearchableFilter } from "./wod-searchable-filter"

interface WodMuscleGroupFilterProps {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function WodMuscleGroupFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: WodMuscleGroupFilterProps) {
  const t = useTranslations("common.wods")
  const { data: muscleGroups, isLoading } = useGetMuscleGroups({ limit: "500", sort: "title" })

  useEffect(() => {
    if (!muscleGroups?.length) return
    const discovered: Record<string, string> = {}
    muscleGroups.forEach(g => { discovered[g.id] = g.title })
    onLabelsDiscovered?.(discovered)
  }, [muscleGroups]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <WodSearchableFilter
      items={muscleGroups ?? []}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("muscle_group_search_placeholder")}
      noResultsText={t("muscle_group_no_results")}
    />
  )
}
