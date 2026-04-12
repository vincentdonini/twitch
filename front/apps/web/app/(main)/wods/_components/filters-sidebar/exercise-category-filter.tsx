"use client"

import { useGetExerciseCategories } from "@workspace/api"
import { OptionState } from "@workspace/ui/components/wod-filters-sidebar"
import { useTranslations } from "next-intl"
import { useEffect } from "react"
import { SearchableFilter } from "./searchable-filter"

interface ExerciseCategoryFilterProps {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function ExerciseCategoryFilter(
  {
    selectedIds,
    getState,
    onToggle,
    onLabelsDiscovered,
  }: ExerciseCategoryFilterProps,
) {
  const t = useTranslations("wods")
  const { data: exerciseCategories, isLoading } = useGetExerciseCategories({ limit: "500", sort: "title" })

  useEffect(() => {
    if (!exerciseCategories?.length) return
    const discovered: Record<string, string> = {}
    exerciseCategories.forEach(e => {
      discovered[e.id] = e.title
    })
    onLabelsDiscovered?.(discovered)
  }, [exerciseCategories]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <SearchableFilter
      items={exerciseCategories ?? []}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("exercise_category_search_placeholder")}
      noResultsText={t("exercise_category_no_results")}
    />
  )
}
