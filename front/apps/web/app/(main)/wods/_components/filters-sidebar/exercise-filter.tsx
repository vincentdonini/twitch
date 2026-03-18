"use client"

import { OptionState } from "@/app/(main)/wods/_lib/filters"
import { useGetExercises } from "@workspace/api"
import { useTranslations } from "next-intl"
import { useEffect } from "react"
import { SearchableFilter } from "./searchable-filter"

interface ExerciseFilterProps {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function ExerciseFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: ExerciseFilterProps) {
  const t = useTranslations("wods")
  const { data: exercises, isLoading } = useGetExercises({ limit: "500", sort: "title" })

  useEffect(() => {
    if (!exercises?.length) return
    const discovered: Record<string, string> = {}
    exercises.forEach(e => { discovered[e.id] = e.title })
    onLabelsDiscovered?.(discovered)
  }, [exercises]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <SearchableFilter
      items={exercises ?? []}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("exercise_search_placeholder")}
      noResultsText={t("exercise_no_results")}
    />
  )
}
