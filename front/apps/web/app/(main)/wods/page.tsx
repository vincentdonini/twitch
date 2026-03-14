"use client"

import React, { useState, useEffect, useRef, useMemo, Suspense } from "react"
import { useTranslations } from "next-intl"
import { useRouter, useSearchParams } from "next/navigation"
import { PageContainer } from "@/ui/components/page-container"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { PaginationControl } from "@workspace/ui/components/pagination-control"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@workspace/ui/components/dropdown-menu"
import { Sheet, SheetContent, SheetTrigger } from "@workspace/ui/components/sheet"
import { Ban, ChevronDown, Funnel, SlidersHorizontal, X } from "lucide-react"
import { useGetWodCategories, useGetWodDivisions, useGetWods, useGetWodTypes } from "@workspace/api"
import { useDebounce } from "@workspace/ui/hooks/use-debounce"
import { WodCard } from "./_components/wod-card"
import { FiltersSidebar } from "./_components/filters-sidebar"
import {
  LIMIT,
  FILTER_BASE,
  sortOptions,
  parseUrl,
  buildUrl,
  type FilterGroupState,
  type SidebarFilterGroup,
} from "./_lib/filters"

export default function Page() {
  return (
    <Suspense>
      <WodsPageContent />
    </Suspense>
  )
}

function WodsPageContent() {
  const t            = useTranslations("wods")
  const router       = useRouter()
  const searchParams = useSearchParams()

  const initial = useMemo(() => parseUrl(searchParams), []) // eslint-disable-line react-hooks/exhaustive-deps

  const [page, setPage]                 = useState(initial.page)
  const [selectedSort, setSelectedSort] = useState(initial.sort)
  const [searchQuery, setSearchQuery]   = useState(initial.search)
  const [filterStates, setFilterStates] = useState<Record<string, FilterGroupState>>(initial.filterStates)
  const debouncedSearch = useDebounce(searchQuery)

  useEffect(() => { setPage(1) }, [debouncedSearch])

  const isMounted = useRef(false)
  useEffect(() => {
    if (!isMounted.current) { isMounted.current = true; return }
    router.replace(buildUrl(filterStates, page, selectedSort, debouncedSearch), { scroll: false })
  }, [filterStates, page, selectedSort, debouncedSearch]) // eslint-disable-line react-hooks/exhaustive-deps

  const { data: wodCategories, isLoading: isLoadingWodCategories } = useGetWodCategories({ limit: "100" })
  const { data: wodTypes,      isLoading: isLoadingWodTypes }      = useGetWodTypes({ limit: "100" })
  const { data: wodDivisions,  isLoading: isLoadingWodDivisions }  = useGetWodDivisions({ limit: "100" })

  const toggleFilter = (groupId: string, optionId: string) => {
    setPage(1)
    setFilterStates(prev => {
      const current  = prev[groupId] ?? { include: [], exclude: [] }
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

  const clearFilterGroup = (groupId: string) => {
    setPage(1)
    if (groupId === "search") { setSearchQuery(""); return }
    setFilterStates(prev => { const next = { ...prev }; delete next[groupId]; return next })
  }

  const clearAllFilters = () => {
    setPage(1)
    setFilterStates({})
    setSearchQuery("")
  }

  const apiParams: Record<string, string | string[]> = {
    page:  String(page),
    limit: String(LIMIT),
    sort:  selectedSort,
    ...(debouncedSearch ? { "filters[name][like]": debouncedSearch } : {}),
  }
  for (const [groupId, state] of Object.entries(filterStates)) {
    const base = FILTER_BASE[groupId]
    if (!base) continue
    if (state.include.length === 1) apiParams[`${base}[eq]`]    = state.include[0]!
    if (state.include.length > 1)   apiParams[`${base}[in]`]    = state.include
    if (state.exclude.length === 1) apiParams[`${base}[neq]`]   = state.exclude[0]!
    if (state.exclude.length > 1)   apiParams[`${base}[notIn]`] = state.exclude
  }

  const { data: wods, isLoading, error, pagination } = useGetWods(apiParams)

  const filterGroups: SidebarFilterGroup[] = [
    {
      id: "category",
      title: t("category"),
      isLoading: isLoadingWodCategories,
      options: wodCategories?.map(c => ({ id: c.id, label: c.title })) ?? [],
      getState: (id) => filterStates.category?.include.includes(id) ? "include"
        : filterStates.category?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("category", id),
    },
    {
      id: "type",
      title: t("type"),
      isLoading: isLoadingWodTypes,
      options: wodTypes?.map(tp => ({ id: tp.id, label: tp.title })) ?? [],
      getState: (id) => filterStates.type?.include.includes(id) ? "include"
        : filterStates.type?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("type", id),
    },
    {
      id: "division",
      title: t("division"),
      isLoading: isLoadingWodDivisions,
      options: wodDivisions?.map(d => ({ id: d.id, label: d.title })) ?? [],
      getState: (id) => filterStates.division?.include.includes(id) ? "include"
        : filterStates.division?.exclude.includes(id) ? "exclude" : "none",
      onToggle: (id) => toggleFilter("division", id),
    },
  ]

  const activeFilterBadges: { key: string; label: string; mode: "include" | "exclude" }[] = [
    ...(debouncedSearch ? [{ key: "search", label: `"${debouncedSearch}"`, mode: "include" as const }] : []),
    ...filterGroups.flatMap(group =>
      group.options.flatMap(opt => {
        const state = group.getState(opt.id)
        if (state === "none") return []
        return [{ key: `${group.id}:${opt.id}`, label: opt.label, mode: state }]
      }),
    ),
  ]

  return (
    <PageContainer size="wide" withPadding={true}>

      {/* Mobile filter button */}
      <div className="mb-4 md:hidden">
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="outline" className="w-full">
              <Funnel className="mr-2 h-4 w-4" />
              {t("filters")}
              {Object.keys(filterStates).length > 0 && (
                <Badge variant="secondary" className="ml-1 text-xs">{Object.keys(filterStates).length}</Badge>
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
        <div id="sidebar" className="hidden md:block col-span-2">
          <FiltersSidebar filterGroups={filterGroups} searchQuery={searchQuery} onSearchChange={setSearchQuery} />
        </div>

        {/* WODs grid */}
        <div id="wod-grid" className="col-span-6 md:col-span-4 min-h-[400px]">

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
                      onClick={() => { setSelectedSort(option.id); setPage(1) }}
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
                  variant="secondary"
                  className={f.mode === "exclude" ? "text-destructive" : ""}
                >
                  {f.mode === "exclude" && <Ban className="mr-1 size-3" />}
                  {f.label}
                  <Button
                    variant="ghost"
                    size="sm"
                    className="h-auto cursor-pointer !p-1 text-inherit"
                    onClick={() => {
                      if (f.key === "search") { clearFilterGroup("search"); return }
                      const [groupId, optionId] = f.key.split(":") as [string, string]
                      toggleFilter(groupId, optionId)
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
            <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
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
                <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
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
