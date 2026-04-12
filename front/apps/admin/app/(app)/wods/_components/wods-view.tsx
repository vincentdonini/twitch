"use client"

import { useGetWodCategories, useGetWodDivisions, useGetWods, useGetWodTypes, type Wod } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@workspace/ui/components/dropdown-menu"
import { Sheet, SheetContent, SheetTrigger } from "@workspace/ui/components/sheet"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { PaginationControl } from "@workspace/ui/components/pagination-control"
import { WodCard } from "@workspace/ui/components/wod-card"
import { FilterGroupState, FilterItem, OptionState, WodFiltersSidebar, isFilterGroupSection } from "@workspace/ui/components/wod-filters-sidebar"
import { useDebounce } from "@workspace/ui/hooks/use-debounce"
import { WodSortOptions } from "@workspace/ui/lib/wods/filters"
import { Ban, ChevronDown, Funnel, SlidersHorizontal, X } from "lucide-react"
import { useTranslations } from "next-intl"
import { useEffect, useRef, useState } from "react"
import {
  WodMuscleAreaFilter,
  WodMuscleGroupFilter,
  WodMuscleSegmentFilter,
} from "@workspace/ui/components/wod-filters-sidebar"
import { EquipmentFilter, ExerciseCategoryFilter, ExerciseFilter, MuscleFilter } from "./wod-searchable-filters"

const LIMIT = 6

const FILTER_BASE: Record<string, string> = {
  category: "filters[category.id]",
  type: "filters[type.id]",
  division: "filters[division.id]",
  exercise: "filters[exercise.id]",
  exerciseCategory: "filters[exerciseCategory.id]",
  equipment: "filters[equipment.id]",
  muscle: "filters[muscle.id]",
  muscleArea: "filters[muscleArea.id]",
  muscleGroup: "filters[muscleGroup.id]",
  muscleSegment: "filters[muscleSegment.id]",
}

const sortOptions = WodSortOptions

// ── Filter state helpers ───────────────────────────────────────────────────────

function toggleFilter(
  prev: Record<string, FilterGroupState>,
  groupId: string,
  optionId: string,
): Record<string, FilterGroupState> {
  const current = prev[groupId] ?? { include: [], exclude: [] }
  const included = current.include.includes(optionId)
  const excluded = current.exclude.includes(optionId)

  let include = current.include
  let exclude = current.exclude

  if (!included && !excluded) {
    include = [...include, optionId]
  } else if (included) {
    include = include.filter(id => id !== optionId)
    exclude = [...exclude, optionId]
  } else {
    exclude = exclude.filter(id => id !== optionId)
  }

  const next = { ...prev }
  if (include.length === 0 && exclude.length === 0) {
    delete next[groupId]
  } else {
    next[groupId] = { include, exclude }
  }
  return next
}

function removeFilter(
  prev: Record<string, FilterGroupState>,
  groupId: string,
  optionId: string,
): Record<string, FilterGroupState> {
  const current = prev[groupId]
  if (!current) return prev
  const include = current.include.filter(id => id !== optionId)
  const exclude = current.exclude.filter(id => id !== optionId)
  const next = { ...prev }
  if (include.length === 0 && exclude.length === 0) {
    delete next[groupId]
  } else {
    next[groupId] = { include, exclude }
  }
  return next
}

// ── WodsView ───────────────────────────────────────────────────────────────────

export function WodsView() {

  const t = useTranslations("wods")
  const tc = useTranslations("common.wods")

  const [page, setPage] = useState(1)
  const [selectedSort, setSelectedSort] = useState("name")
  const [searchQuery, setSearchQuery] = useState("")
  const [filterStates, setFilterStates] = useState<Record<string, FilterGroupState>>({})
  const [exerciseLabels, setExerciseLabels] = useState<Record<string, string>>({})
  const [exerciseCategoryLabels, setExerciseCategoryLabels] = useState<Record<string, string>>({})
  const [equipmentLabels, setEquipmentLabels] = useState<Record<string, string>>({})
  const [muscleLabels, setMuscleLabels] = useState<Record<string, string>>({})
  const [muscleAreaLabels, setMuscleAreaLabels] = useState<Record<string, string>>({})
  const [muscleGroupLabels, setMuscleGroupLabels] = useState<Record<string, string>>({})
  const [muscleSegmentLabels, setMuscleSegmentLabels] = useState<Record<string, string>>({})

  const debouncedSearch = useDebounce(searchQuery)
  const isMounted = useRef(false)

  useEffect(() => {
    setPage(1)
  }, [debouncedSearch]) // eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => {
    if (!isMounted.current) {
      isMounted.current = true
      return
    }
    setPage(1)
  }, [filterStates]) // eslint-disable-line react-hooks/exhaustive-deps

  // Fetch filter options
  const { data: wodCategoriesData, isLoading: isLoadingCategories } = useGetWodCategories({ limit: "100" })
  const { data: wodTypesData, isLoading: isLoadingTypes } = useGetWodTypes({ limit: "100" })
  const { data: wodDivisionsData, isLoading: isLoadingDivisions } = useGetWodDivisions({ limit: "100" })
  const wodCategories = wodCategoriesData ?? []
  const wodTypes = wodTypesData ?? []
  const wodDivisions = wodDivisionsData ?? []

  // Build API params
  const apiParams: Record<string, string | string[]> = {
    page: String(page),
    limit: String(LIMIT),
    sort: selectedSort,
    ...(debouncedSearch ? { "filters[name][like]": debouncedSearch } : {}),
  }
  for (const [groupId, state] of Object.entries(filterStates)) {
    const base = FILTER_BASE[groupId]
    if (!base) continue
    if (state.include.length === 1) apiParams[`${base}[eq]`] = state.include[0]!
    if (state.include.length > 1) apiParams[`${base}[in]`] = state.include
    if (state.exclude.length === 1) apiParams[`${base}[neq]`] = state.exclude[0]!
    if (state.exclude.length > 1) apiParams[`${base}[notIn]`] = state.exclude
  }

  const { data: wods, isLoading, error, pagination } = useGetWods(apiParams)

  // Filter group state helpers
  const getState = (groupId: string) => (optionId: string): OptionState => {
    if (filterStates[groupId]?.include.includes(optionId)) return "include"
    if (filterStates[groupId]?.exclude.includes(optionId)) return "exclude"
    return "none"
  }

  const onToggle = (groupId: string) => (optionId: string) => {
    setFilterStates(prev => toggleFilter(prev, groupId, optionId))
  }

  const clearFilterGroup = (groupId: string) => {
    setPage(1)
    if (groupId === "search") {
      setSearchQuery("")
      return
    }
    setFilterStates(prev => {
      const next = { ...prev }
      delete next[groupId]
      return next
    })
  }

  const clearAllFilters = () => {
    setPage(1)
    setFilterStates({})
    setSearchQuery("")
  }

  // Filter groups config
  const filterGroups: FilterItem[] = [
    {
      id: "category",
      title: tc("category"),
      isLoading: isLoadingCategories,
      options: wodCategories.map(c => ({ id: c.id, label: c.title, count: c.wodCount })),
      getState: getState("category"),
      onToggle: onToggle("category"),
    },
    {
      id: "type",
      title: tc("type"),
      isLoading: isLoadingTypes,
      options: wodTypes.map(tp => ({ id: tp.id, label: tp.title, count: tp.wodCount })),
      getState: getState("type"),
      onToggle: onToggle("type"),
    },
    {
      id: "division",
      title: tc("division"),
      isLoading: isLoadingDivisions,
      options: wodDivisions.map(d => ({ id: d.id, label: d.title, count: d.wodCount })),
      getState: getState("division"),
      onToggle: onToggle("division"),
    },
    {
      id: "exercises",
      title: tc("exercises"),
      groups: [
        {
          id: "exercise",
          title: tc("exercise"),
          isLoading: false,
          getState: getState("exercise"),
          onToggle: onToggle("exercise"),
          renderContent: () => (
            <ExerciseFilter
              selectedIds={{ include: filterStates.exercise?.include ?? [], exclude: filterStates.exercise?.exclude ?? [] }}
              getState={getState("exercise")}
              onToggle={onToggle("exercise")}
              onLabelsDiscovered={(labels) => setExerciseLabels(prev => ({ ...prev, ...labels }))}
            />
          ),
        },
        {
          id: "exerciseCategory",
          title: tc("exercise_category"),
          isLoading: false,
          getState: getState("exerciseCategory"),
          onToggle: onToggle("exerciseCategory"),
          renderContent: () => (
            <ExerciseCategoryFilter
              selectedIds={{
                include: filterStates.exerciseCategory?.include ?? [],
                exclude: filterStates.exerciseCategory?.exclude ?? [],
              }}
              getState={getState("exerciseCategory")}
              onToggle={onToggle("exerciseCategory")}
              onLabelsDiscovered={(labels) => setExerciseCategoryLabels(prev => ({ ...prev, ...labels }))}
            />
          ),
        },
        {
          id: "equipment",
          title: tc("equipment"),
          isLoading: false,
          getState: getState("equipment"),
          onToggle: onToggle("equipment"),
          renderContent: () => (
            <EquipmentFilter
              selectedIds={{
                include: filterStates.equipment?.include ?? [],
                exclude: filterStates.equipment?.exclude ?? [],
              }}
              getState={getState("equipment")}
              onToggle={onToggle("equipment")}
              onLabelsDiscovered={(labels) => setEquipmentLabels(prev => ({ ...prev, ...labels }))}
            />
          ),
        },
      ],
    },
    {
      id: "muscles",
      title: tc("muscles"),
      groups: [
        {
          id: "muscleArea",
          title: tc("muscleArea"),
          isLoading: false,
          getState: getState("muscleArea"),
          onToggle: onToggle("muscleArea"),
          renderContent: () => (
            <WodMuscleAreaFilter
              selectedIds={{
                include: filterStates.muscleArea?.include ?? [],
                exclude: filterStates.muscleArea?.exclude ?? [],
              }}
              getState={getState("muscleArea")}
              onToggle={onToggle("muscleArea")}
              onLabelsDiscovered={(labels) => setMuscleAreaLabels(prev => ({ ...prev, ...labels }))}
            />
          ),
        },
        {
          id: "muscleGroup",
          title: tc("muscleGroup"),
          isLoading: false,
          getState: getState("muscleGroup"),
          onToggle: onToggle("muscleGroup"),
          renderContent: () => (
            <WodMuscleGroupFilter
              selectedIds={{
                include: filterStates.muscleGroup?.include ?? [],
                exclude: filterStates.muscleGroup?.exclude ?? [],
              }}
              getState={getState("muscleGroup")}
              onToggle={onToggle("muscleGroup")}
              onLabelsDiscovered={(labels) => setMuscleGroupLabels(prev => ({ ...prev, ...labels }))}
            />
          ),
        },
        {
          id: "muscle",
          title: tc("muscle"),
          isLoading: false,
          getState: getState("muscle"),
          onToggle: onToggle("muscle"),
          renderContent: () => (
            <MuscleFilter
              selectedIds={{
                include: filterStates.muscle?.include ?? [],
                exclude: filterStates.muscle?.exclude ?? [],
              }}
              getState={getState("muscle")}
              onToggle={onToggle("muscle")}
              onLabelsDiscovered={(labels) => setMuscleLabels(prev => ({ ...prev, ...labels }))}
            />
          ),
        },
        {
          id: "muscleSegment",
          title: tc("muscleSegment"),
          isLoading: false,
          getState: getState("muscleSegment"),
          onToggle: onToggle("muscleSegment"),
          renderContent: () => (
            <WodMuscleSegmentFilter
              selectedIds={{
                include: filterStates.muscleSegment?.include ?? [],
                exclude: filterStates.muscleSegment?.exclude ?? [],
              }}
              getState={getState("muscleSegment")}
              onToggle={onToggle("muscleSegment")}
              onLabelsDiscovered={(labels) => setMuscleSegmentLabels(prev => ({ ...prev, ...labels }))}
            />
          ),
        },
      ],
    },
  ]

  // Groups with renderContent (searchable) track state via filterStates directly
  const searchableGroups = ["exercise", "exerciseCategory", "equipment", "muscle", "muscleArea", "muscleGroup", "muscleSegment"]
  const labelCacheFor = (id: string) =>
    id === "exercise" ? exerciseLabels
      : id === "exerciseCategory" ? exerciseCategoryLabels
        : id === "equipment" ? equipmentLabels
          : id === "muscle" ? muscleLabels
            : id === "muscleArea" ? muscleAreaLabels
              : id === "muscleGroup" ? muscleGroupLabels
                : muscleSegmentLabels

  const allGroups = filterGroups.flatMap(item =>
    isFilterGroupSection(item) ? item.groups : [item]
  )

  const activeFilterBadges: { key: string; label: string; mode: OptionState }[] = [
    ...(debouncedSearch ? [{ key: "search", label: `"${debouncedSearch}"`, mode: OptionState.Include }] : []),
    ...allGroups.flatMap(group => {
      if (searchableGroups.includes(group.id)) {
        const cache = labelCacheFor(group.id)
        const state = filterStates[group.id] ?? { include: [], exclude: [] }
        return [
          ...state.include.map(id => ({
            key: `${group.id}:${id}`,
            label: cache[id] ?? `${id.substring(0, 8)}…`,
            mode: OptionState.Include,
          })),
          ...state.exclude.map(id => ({
            key: `${group.id}:${id}`,
            label: cache[id] ?? `${id.substring(0, 8)}…`,
            mode: OptionState.Exclude,
          })),
        ]
      }

      return (group.options ?? []).flatMap(opt => {
        const state = group.getState(opt.id)
        if (state === OptionState.None) return []
        return [{ key: `${group.id}:${opt.id}`, label: opt.label, mode: state }]
      })
    }),
  ]

  return (
    <div>
      {/*Mobile filter button*/}
      <div className="mb-4 lg:hidden">
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="outline" className="w-full cursor-pointer">
              <Funnel className="mr-2 h-4 w-4" />
              {tc("filters")}
              {activeFilterBadges.length > 0 && (
                <Badge color="secondary" className="ml-1 text-xs">{activeFilterBadges.length}</Badge>
              )}
            </Button>
          </SheetTrigger>
          <SheetContent side="left" className="w-80 overflow-y-auto">
            <WodFiltersSidebar
              filterGroups={filterGroups}
              searchQuery={searchQuery}
              onSearchChange={setSearchQuery}
            />
          </SheetContent>
        </Sheet>
      </div>

      <div className="grid grid-cols-6 gap-6">
        {/* Sidebar */}
        <div id="sidebar" className="hidden lg:block col-span-2">
          <WodFiltersSidebar
            filterGroups={filterGroups}
            searchQuery={searchQuery}
            onSearchChange={setSearchQuery}
          />
        </div>

        {/* WODs grid */}
        <div id="wod-grid" className="col-span-6 lg:col-span-4 min-h-[400px]">

          {/* Header */}
          <div className="mb-4 w-full">
            <div className="mt-2 flex items-center justify-between gap-4">
              <div className="text-muted-foreground text-sm flex flex-1 items-center gap-2">
                {pagination
                  ? tc("workout_count", { count: pagination.total })
                  : <Skeleton className="h-8 w-24" />
                }
              </div>
              <DropdownMenu>
                <DropdownMenuTrigger asChild size="sm">
                  <Button variant="outline" size="sm" className="shrink-0 cursor-pointer">
                    <SlidersHorizontal className="me-2 size-4" />
                    {tc(sortOptions.find(s => s.id === selectedSort)!.labelKey)}
                    <ChevronDown className="ms-2 size-4" />
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" className="w-48">
                  {sortOptions.map(option => (
                    <DropdownMenuItem
                      key={option.id}
                      onClick={() => {
                        setSelectedSort(option.id)
                        setPage(1)
                      }}
                      className={selectedSort === option.id ? "bg-accent" : ""}
                    >
                      {tc(option.labelKey)}
                    </DropdownMenuItem>
                  ))}
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>

          {/* Active filters */}
          {activeFilterBadges.length > 0 && (
            <div className="mb-4 flex flex-wrap items-center gap-2">
              <span className="text-muted-foreground text-sm font-medium">{tc("active_filters")}</span>
              {activeFilterBadges.map(f => (
                <Badge
                  key={f.key}
                  color="secondary"
                  className={f.mode === "exclude" ? "text-destructive" : ""}
                >
                  {f.mode === "exclude" && <Ban className="mr-1 size-3" />}
                  {f.label}
                  <Button
                    variant="ghost"
                    size="sm"
                    className="h-auto cursor-pointer !p-1 text-inherit"
                    onClick={() => {
                      if (f.key === "search") {
                        clearFilterGroup("search")
                        return
                      }
                      const [groupId, optionId] = f.key.split(":") as [string, string]
                      setFilterStates(prev => removeFilter(prev, groupId, optionId))
                    }}
                  >
                    <X className="size-3" />
                  </Button>
                </Badge>
              ))}
              <Button
                variant="ghost"
                size="sm"
                onClick={clearAllFilters}
                className="text-muted-foreground h-auto cursor-pointer p-1.5 text-xs"
              >
                {tc("clear_all")}
              </Button>
            </div>
          )}

          {/* Loading */}
          {isLoading && (
            <div className="grid grid-cols-1 gap-4">
              {Array.from({ length: LIMIT }).map((_, i) => (
                <Skeleton key={i} className="h-38 w-full rounded-lg" />
              ))}
            </div>
          )}

          {/* Error */}
          {error && (
            <div className="text-muted-foreground rounded-lg border p-8 text-center text-sm">{t("error")}</div>
          )}

          {/*Results*/}
          {!isLoading && !error && wods && (
            <>
              {wods.length === 0 ? (
                <div className="text-muted-foreground rounded-lg border p-8 text-center text-sm">
                  {tc("no_results")}
                </div>
              ) : (
                <div className="grid grid-cols-1 gap-4">
                  {wods.map((wod: Wod) => <WodCard key={wod.id} wod={wod} />)}
                </div>
              )}

              {pagination && pagination.totalPages > 1 && (
                <div className="mt-8">
                  <PaginationControl pagination={pagination} onPageChange={setPage} />
                </div>
              )}
            </>
          )}
        </div>
      </div>
    </div>
  )
}
