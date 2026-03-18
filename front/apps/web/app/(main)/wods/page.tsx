"use client"

import { FiltersSidebar } from "@/app/(main)/wods/_components/filters-sidebar"
import { WodCard } from "@/app/(main)/wods/_components/wod-card"
import {
  buildUrl,
  FILTER_BASE,
  FilterGroupState,
  LIMIT,
  parseUrl,
  SidebarFilterGroup,
  sortOptions,
} from "@/app/(main)/wods/_lib/filters"
import { PageContainer } from "@/ui/components/page-container"
import { useGetWodCategories, useGetWodDivisions, useGetWods, useGetWodTypes } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@workspace/ui/components/dropdown-menu"
import { PaginationControl } from "@workspace/ui/components/pagination-control"
import { Sheet, SheetContent, SheetTrigger } from "@workspace/ui/components/sheet"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { useDebounce } from "@workspace/ui/hooks/use-debounce"
import { Ban, ChevronDown, Funnel, SlidersHorizontal, X } from "lucide-react"
import { useTranslations } from "next-intl"
import { useRouter, useSearchParams } from "next/navigation"
import React, { Suspense, useEffect, useMemo, useRef, useState } from "react"

export default function Page() {
  return (
    <Suspense>
      <WodsPageContent />
    </Suspense>
  )
}

function WodsPageContent() {
  const t = useTranslations("wods")
  const router = useRouter()
  const searchParams = useSearchParams()

  const initial = useMemo(() => parseUrl(searchParams), []) // eslint-disable-line react-hooks/exhaustive-deps

  const [page, setPage] = useState(initial.page)
  const [selectedSort, setSelectedSort] = useState(initial.sort)
  const [searchQuery, setSearchQuery] = useState(initial.search)
  const [filterStates, setFilterStates] = useState<Record<string, FilterGroupState>>(initial.filterStates)
  const [exerciseLabels, setExerciseLabels] = useState<Record<string, string>>({})
  const [exerciseCategoryLabels, setExerciseCategoryLabels] = useState<Record<string, string>>({})
  const [equipmentLabels, setEquipmentLabels] = useState<Record<string, string>>({})
  const debouncedSearch = useDebounce(searchQuery)

  useEffect(() => {
    setPage(1)
  }, [debouncedSearch])

  const isMounted = useRef(false)
  useEffect(() => {
    if (!isMounted.current) {
      isMounted.current = true
      return
    }
    router.replace(buildUrl(filterStates, page, selectedSort, debouncedSearch), { scroll: false })
  }, [filterStates, page, selectedSort, debouncedSearch]) // eslint-disable-line react-hooks/exhaustive-deps

  const { data: wodCategories, isLoading: isLoadingWodCategories } = useGetWodCategories({ limit: "100" })
  const { data: wodTypes, isLoading: isLoadingWodTypes } = useGetWodTypes({ limit: "100" })
  const { data: wodDivisions, isLoading: isLoadingWodDivisions } = useGetWodDivisions({ limit: "100" })

  const toggleFilter = (groupId: string, optionId: string) => {
    setPage(1)
    setFilterStates(prev => {
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
    })
  }

  const removeFilter = (groupId: string, optionId: string) => {
    setPage(1)
    setFilterStates(prev => {
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
    })
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

  const filterGroups: SidebarFilterGroup[] = [
    {
      id: "category",
      title: t("category"),
      isLoading: isLoadingWodCategories,
      options: wodCategories?.map(c => ({ id: c.id, label: c.title, count: c.wodCount })) ?? [],
      getState: (id) => filterStates.category?.include.includes(id) ? "include"
        : filterStates.category?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("category", id),
    },
    {
      id: "type",
      title: t("type"),
      isLoading: isLoadingWodTypes,
      options: wodTypes?.map(tp => ({ id: tp.id, label: tp.title, count: tp.wodCount })) ?? [],
      getState: (id) => filterStates.type?.include.includes(id) ? "include"
        : filterStates.type?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("type", id),
    },
    {
      id: "division",
      title: t("division"),
      isLoading: isLoadingWodDivisions,
      options: wodDivisions?.map(d => ({ id: d.id, label: d.title, count: d.wodCount })) ?? [],
      getState: (id) => filterStates.division?.include.includes(id) ? "include"
        : filterStates.division?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("division", id),
    },
    {
      id: "exercise",
      title: t("exercise"),
      isLoading: false,
      options: [],
      searchable: true,
      selectedIds: {
        include: filterStates.exercise?.include ?? [],
        exclude: filterStates.exercise?.exclude ?? [],
      },
      labelCache: exerciseLabels,
      onLabelsDiscovered: (labels) => setExerciseLabels(prev => ({ ...prev, ...labels })),
      getState: (id) => filterStates.exercise?.include.includes(id) ? "include"
        : filterStates.exercise?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("exercise", id),
    },
    {
      id: "exerciseCategory",
      title: t("exercise_category"),
      isLoading: false,
      options: [],
      searchable: true,
      selectedIds: {
        include: filterStates.exerciseCategory?.include ?? [],
        exclude: filterStates.exerciseCategory?.exclude ?? [],
      },
      labelCache: exerciseCategoryLabels,
      onLabelsDiscovered: (labels) => setExerciseCategoryLabels(prev => ({ ...prev, ...labels })),
      getState: (id) => filterStates.exerciseCategory?.include.includes(id) ? "include"
        : filterStates.exerciseCategory?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("exerciseCategory", id),
    },
    {
      id: "equipment",
      title: t("equipment"),
      isLoading: false,
      options: [],
      searchable: true,
      selectedIds: {
        include: filterStates.equipment?.include ?? [],
        exclude: filterStates.equipment?.exclude ?? [],
      },
      labelCache: equipmentLabels,
      onLabelsDiscovered: (labels) => setEquipmentLabels(prev => ({ ...prev, ...labels })),
      getState: (id) => filterStates.equipment?.include.includes(id) ? "include"
        : filterStates.equipment?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("equipment", id),
    },
  ]

  const activeFilterBadges: { key: string; label: string; mode: "include" | "exclude" }[] = [
    ...(debouncedSearch ? [{ key: "search", label: `"${debouncedSearch}"`, mode: "include" as const }] : []),
    ...filterGroups.flatMap(group => {
      if (group.searchable && group.selectedIds) {
        return [
          ...group.selectedIds.include.map(id => ({
            key: `${group.id}:${id}`,
            label: group.labelCache?.[id] ?? `${id.substring(0, 8)}…`,
            mode: "include" as const,
          })),
          ...group.selectedIds.exclude.map(id => ({
            key: `${group.id}:${id}`,
            label: group.labelCache?.[id] ?? `${id.substring(0, 8)}…`,
            mode: "exclude" as const,
          })),
        ]
      }
      return group.options.flatMap(opt => {
        const state = group.getState(opt.id)
        if (state === "none") return []
        return [{ key: `${group.id}:${opt.id}`, label: opt.label, mode: state }]
      })
    }),
  ]

  return (
    <PageContainer size="wide" withPadding={true}>

      {/* Mobile filter button */}
      <div className="mb-4 lg:hidden">
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="outline" className="w-full">
              <Funnel className="mr-2 h-4 w-4" />
              {t("filters")}
              {Object.keys(filterStates).length > 0 && (
                <Badge color="secondary" className="ml-1 text-xs">{Object.keys(filterStates).length}</Badge>
              )}
            </Button>
          </SheetTrigger>
          <SheetContent side="left" className="w-80 overflow-y-auto">
            <FiltersSidebar filterGroups={filterGroups} searchQuery={searchQuery} onSearchChange={setSearchQuery} />
          </SheetContent>
        </Sheet>
      </div>

      <div className="grid grid-cols-6 gap-6">

        {/* Sidebar */}
        <div id="sidebar" className="hidden lg:block col-span-2">
          <FiltersSidebar filterGroups={filterGroups} searchQuery={searchQuery} onSearchChange={setSearchQuery} />
        </div>

        {/* WODs grid */}
        <div id="wod-grid" className="col-span-6 lg:col-span-4 min-h-[400px]">

          {/* Header */}
          <div className="mb-4 w-full">
            <h2 className="text-3xl font-bold tracking-tight text-balance">{t("title")}</h2>
            <div className="mt-2 flex items-center justify-between gap-4">
              <div className="text-muted-foreground text-sm flex flex-1 items-center gap-2">
                {pagination
                  ? t("workout_count", { count: pagination.total })
                  : <Skeleton className="h-8 w-24" />
                }
              </div>
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="outline" size="sm" className="shrink-0 cursor-pointer">
                    <SlidersHorizontal className="me-2 size-4" />
                    {t(sortOptions.find(s => s.id === selectedSort)!.labelKey)}
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
                      {t(option.labelKey)}
                    </DropdownMenuItem>
                  ))}
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>

          {/* Active Filters */}
          {activeFilterBadges.length > 0 && (
            <div className="mb-4 flex flex-wrap items-center gap-2">
              <span className="text-muted-foreground text-sm font-medium">{t("active_filters")}</span>
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
                      removeFilter(groupId, optionId)
                    }}
                  >
                    <X className="size-3" />
                  </Button>
                </Badge>
              ))}
              <DropdownMenuSeparator className="mx-2" />
              <Button
                variant="ghost"
                size="sm"
                onClick={clearAllFilters}
                className="text-muted-foreground h-auto cursor-pointer p-1.5 text-xs"
              >
                {t("clear_all")}
              </Button>
            </div>
          )}

          {/* WOD list */}
          {isLoading && (
            <div className="grid grid-cols-1 gap-4">
              {Array.from({ length: LIMIT }).map((_, i) => (
                <Skeleton key={i} className="h-38 w-full rounded-lg" />
              ))}
            </div>
          )}

          {error && (
            <div className="text-muted-foreground rounded-lg border p-8 text-center text-sm">{error}</div>
          )}

          {!isLoading && !error && wods && (
            <>
              {wods.length === 0 ? (
                <div className="text-muted-foreground rounded-lg border p-8 text-center text-sm">
                  {t("no_results")}
                </div>
              ) : (
                <div className="grid grid-cols-1 gap-4">
                  {wods.map(wod => <WodCard key={wod.id} wod={wod} />)}
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
    </PageContainer>
  )
}
