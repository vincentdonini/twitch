"use client"

import { EquipmentFilter } from "@/app/(main)/wods/_components/filters-sidebar/equipment-filter"
import { ExerciseCategoryFilter } from "@/app/(main)/wods/_components/filters-sidebar/exercise-category-filter"
import { ExerciseFilter } from "@/app/(main)/wods/_components/filters-sidebar/exercise-filter"
import { MuscleFilter } from "@/app/(main)/wods/_components/filters-sidebar/muscle-filter"
import { WodMuscleAreaFilter, WodMuscleGroupFilter, WodMuscleSegmentFilter } from "@workspace/ui/components/wod-filters-sidebar"
import { FilterGroup } from "@/app/(main)/wods/_components/filters-sidebar/filter-group"
import { TriStateCheckbox } from "@/app/(main)/wods/_components/filters-sidebar/tri-state-checkbox"
import { SidebarFilterGroup } from "@/app/(main)/wods/_lib/filters"
import { Card, CardContent } from "@workspace/ui/components/card"
import { Input } from "@workspace/ui/components/input"
import { Stack } from "@workspace/ui/components/stack"
import { Search } from "lucide-react"
import { useTranslations } from "next-intl"

interface FiltersSidebarProps {
  filterGroups: SidebarFilterGroup[]
  searchQuery: string
  onSearchChange: (q: string) => void
}

export function FiltersSidebar({ filterGroups, searchQuery, onSearchChange }: FiltersSidebarProps) {
  const t = useTranslations("wods")
  return (
    <Card className="shadow-none border-0 sm:border-1">
      <CardContent>
        <Stack gap={6} divider>
          <div>
            <h4 className="text-lg font-medium">{t("filters")}</h4>
            <div className="relative mt-4">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder={t("search_placeholder")}
                value={searchQuery}
                onChange={e => onSearchChange(e.target.value)}
                className="pl-9"
              />
            </div>
          </div>
          {filterGroups.map(group => (
            <FilterGroup key={group.id} title={group.title} isLoading={group.isLoading}>
              {group.searchable && group.id === "exercise" ? (
                <ExerciseFilter
                  selectedIds={group.selectedIds ?? { include: [], exclude: [] }}
                  getState={group.getState}
                  onToggle={group.onToggle}
                  onLabelsDiscovered={group.onLabelsDiscovered}
                />
              ) : group.searchable && group.id === "exerciseCategory" ? (
                <ExerciseCategoryFilter
                  selectedIds={group.selectedIds ?? { include: [], exclude: [] }}
                  getState={group.getState}
                  onToggle={group.onToggle}
                  onLabelsDiscovered={group.onLabelsDiscovered}
                />
              ) : group.searchable && group.id === "equipment" ? (
                <EquipmentFilter
                  selectedIds={group.selectedIds ?? { include: [], exclude: [] }}
                  getState={group.getState}
                  onToggle={group.onToggle}
                  onLabelsDiscovered={group.onLabelsDiscovered}
                />
              ) : group.searchable && group.id === "muscle" ? (
                <MuscleFilter
                  selectedIds={group.selectedIds ?? { include: [], exclude: [] }}
                  getState={group.getState}
                  onToggle={group.onToggle}
                  onLabelsDiscovered={group.onLabelsDiscovered}
                />
              ) : group.searchable && group.id === "muscleArea" ? (
                <WodMuscleAreaFilter
                  selectedIds={group.selectedIds ?? { include: [], exclude: [] }}
                  getState={group.getState}
                  onToggle={group.onToggle}
                  onLabelsDiscovered={group.onLabelsDiscovered}
                />
              ) : group.searchable && group.id === "muscleGroup" ? (
                <WodMuscleGroupFilter
                  selectedIds={group.selectedIds ?? { include: [], exclude: [] }}
                  getState={group.getState}
                  onToggle={group.onToggle}
                  onLabelsDiscovered={group.onLabelsDiscovered}
                />
              ) : group.searchable && group.id === "muscleSegment" ? (
                <WodMuscleSegmentFilter
                  selectedIds={group.selectedIds ?? { include: [], exclude: [] }}
                  getState={group.getState}
                  onToggle={group.onToggle}
                  onLabelsDiscovered={group.onLabelsDiscovered}
                />
              ) : (
                group.options.map(option => (
                  <TriStateCheckbox
                    key={option.id}
                    label={option.label}
                    count={option.count}
                    state={group.getState(option.id)}
                    onToggle={() => group.onToggle(option.id)}
                  />
                ))
              )}
            </FilterGroup>
          ))}
        </Stack>
      </CardContent>
    </Card>
  )
}
