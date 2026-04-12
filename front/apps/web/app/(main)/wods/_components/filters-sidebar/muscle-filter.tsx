"use client"

import { OptionState } from "@/app/(main)/wods/_lib/filters"
import { useGetMuscles } from "@workspace/api"
import { useTranslations } from "next-intl"
import { useEffect } from "react"
import { SearchableFilter } from "./searchable-filter"

interface MuscleFilterProps {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function MuscleFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: MuscleFilterProps) {
  const t = useTranslations("wods")
  const { data: muscles, isLoading } = useGetMuscles({ limit: "500", sort: "title" })

  useEffect(() => {
    if (!muscles?.length) return
    const discovered: Record<string, string> = {}
    muscles.forEach(m => { discovered[m.id] = m.title })
    onLabelsDiscovered?.(discovered)
  }, [muscles]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <SearchableFilter
      items={muscles ?? []}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("muscle_search_placeholder")}
      noResultsText={t("muscle_no_results")}
    />
  )
}
