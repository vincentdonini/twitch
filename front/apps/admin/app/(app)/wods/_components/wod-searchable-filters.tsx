"use client"

import { useGetEquipments, useGetExerciseCategories, useGetExercises, useGetMuscles } from "@workspace/api"
import {
  type OptionState,
  WodSearchableFilter,
} from "@workspace/ui/components/wod-filters-sidebar"
import { useTranslations } from "next-intl"
import { useEffect } from "react"

interface Props {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function ExerciseFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: Props) {
  const t = useTranslations("common.wods")
  const { data: exercisesRaw, isLoading } = useGetExercises({ limit: "500", sort: "title" })
  const exercises = exercisesRaw ?? []

  useEffect(() => {
    if (!exercises.length) return
    const discovered: Record<string, string> = {}
    exercises.forEach(e => { discovered[e.id] = e.title })
    onLabelsDiscovered?.(discovered)
  }, [exercises]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <WodSearchableFilter
      items={exercises}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("exercise_search_placeholder")}
      noResultsText={t("exercise_no_results")}
    />
  )
}

export function ExerciseCategoryFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: Props) {
  const t = useTranslations("common.wods")
  const { data: categoriesRaw, isLoading } = useGetExerciseCategories({ limit: "500", sort: "title" })
  const categories = categoriesRaw ?? []

  useEffect(() => {
    if (!categories.length) return
    const discovered: Record<string, string> = {}
    categories.forEach(e => { discovered[e.id] = e.title })
    onLabelsDiscovered?.(discovered)
  }, [categories]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <WodSearchableFilter
      items={categories}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("exercise_category_search_placeholder")}
      noResultsText={t("exercise_category_no_results")}
    />
  )
}

export function EquipmentFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: Props) {
  const t = useTranslations("common.wods")
  const { data: equipmentsRaw, isLoading } = useGetEquipments({ limit: "500", sort: "title" })
  const equipments = equipmentsRaw ?? []

  useEffect(() => {
    if (!equipments.length) return
    const discovered: Record<string, string> = {}
    equipments.forEach(e => { discovered[e.id] = e.title })
    onLabelsDiscovered?.(discovered)
  }, [equipments]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <WodSearchableFilter
      items={equipments}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("equipment_search_placeholder")}
      noResultsText={t("equipment_no_results")}
    />
  )
}

export function MuscleFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: Props) {
  const t = useTranslations("common.wods")
  const { data: musclesRaw, isLoading } = useGetMuscles({ limit: "500", sort: "title" })
  const muscles = musclesRaw ?? []

  useEffect(() => {
    if (!muscles.length) return
    const discovered: Record<string, string> = {}
    muscles.forEach(m => { discovered[m.id] = m.title })
    onLabelsDiscovered?.(discovered)
  }, [muscles]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <WodSearchableFilter
      items={muscles}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("muscle_search_placeholder")}
      noResultsText={t("muscle_no_results")}
    />
  )
}
