"use client"

import { useGetEquipments } from "@workspace/api"
import { OptionState } from "@workspace/ui/components/wod-filters-sidebar"
import { useTranslations } from "next-intl"
import { useEffect } from "react"
import { SearchableFilter } from "./searchable-filter"

interface EquipmentFilterProps {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export function EquipmentFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: EquipmentFilterProps) {
  const t = useTranslations("wods")
  const { data: equipments, isLoading } = useGetEquipments({ limit: "500", sort: "title" })

  useEffect(() => {
    if (!equipments?.length) return
    const discovered: Record<string, string> = {}
    equipments.forEach(e => {
      discovered[e.id] = e.title
    })
    onLabelsDiscovered?.(discovered)
  }, [equipments]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <SearchableFilter
      items={equipments ?? []}
      isLoading={isLoading}
      selectedIds={selectedIds}
      getState={getState}
      onToggle={onToggle}
      placeholder={t("equipment_search_placeholder")}
      noResultsText={t("equipment_no_results")}
    />
  )
}
