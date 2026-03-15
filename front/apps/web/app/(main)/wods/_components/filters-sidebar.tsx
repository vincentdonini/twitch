"use client"

import React, { useState, useEffect, useRef } from "react"
import { useTranslations } from "next-intl"
import { ChevronDown, Search } from "lucide-react"
import { useGetExercises } from "@workspace/api"
import { useDebounce } from "@workspace/ui/hooks/use-debounce"
import { Button } from "@workspace/ui/components/button"
import { Card, CardContent } from "@workspace/ui/components/card"
import { Checkbox } from "@workspace/ui/components/checkbox"
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@workspace/ui/components/collapsible"
import { Input } from "@workspace/ui/components/input"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { Badge } from "@workspace/ui/components/badge"
import { Stack } from "@workspace/ui/components/stack"
import type { OptionState, SidebarFilterGroup } from "../_lib/filters"


export function FiltersSidebar({ filterGroups, searchQuery, onSearchChange }: {
  filterGroups: SidebarFilterGroup[]
  searchQuery: string
  onSearchChange: (q: string) => void
}) {
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
              {group.searchable ? (
                <ExerciseSearchFilter
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

function FilterGroup({ title, isLoading, children }: {
  title: string
  isLoading: boolean
  children: React.ReactNode
}) {
  return (
    <Collapsible defaultOpen>
      <div className="flex items-center justify-between">
        <h4 className="text-lg font-medium">{title}</h4>
        <CollapsibleTrigger asChild>
          <Button variant="ghost" size="icon">
            <ChevronDown className="h-5 w-5" />
          </Button>
        </CollapsibleTrigger>
      </div>
      <CollapsibleContent className="space-y-3 pt-4">
        {isLoading
          ? Array.from({ length: 3 }).map((_, i) => (
            <div key={i} className="flex items-center gap-3">
              <Skeleton className="size-4 rounded-[4px]" />
              <Skeleton className="h-4 w-full rounded" />
            </div>
          ))
          : children
        }
      </CollapsibleContent>
    </Collapsible>
  )
}


// ---------------------------------------------------------------------------
// ExerciseSearchFilter
// ---------------------------------------------------------------------------

function ExerciseSearchFilter({ selectedIds, getState, onToggle, onLabelsDiscovered }: {
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}) {
  const t = useTranslations("wods")
  const [search, setSearch] = useState("")
  const debouncedSearch = useDebounce(search)
  const labelMap = useRef<Record<string, string>>({})
  const countMap = useRef<Record<string, number>>({})

  const { data: results, isLoading: isSearching } = useGetExercises(
    debouncedSearch
      ? { "filters[title][like]": debouncedSearch, limit: "15" }
      : { limit: "1" }
  )

  useEffect(() => {
    if (!results?.length) return
    const discovered: Record<string, string> = {}
    results.forEach(e => {
      labelMap.current[e.id] = e.title
      countMap.current[e.id] = e.wodCount
      discovered[e.id] = e.title
    })
    onLabelsDiscovered?.(discovered)
  }, [results]) // eslint-disable-line react-hooks/exhaustive-deps

  const allSelectedIds    = [...new Set([...selectedIds.include, ...selectedIds.exclude])]
  const searchResults     = debouncedSearch ? (results ?? []) : []
  const unselectedResults = searchResults.filter(e => getState(e.id) === "none")

  return (
    <div className="space-y-3">

      {/* Exercises already selected (include / exclude) */}
      {allSelectedIds.length > 0 && (
        <>
          <div className="space-y-2">
            {allSelectedIds.map(id => (
              <TriStateCheckbox
                key={id}
                label={labelMap.current[id] ?? `${id.substring(0, 8)}…`}
                count={countMap.current[id]}
                state={getState(id)}
                onToggle={() => onToggle(id)}
              />
            ))}
          </div>
          <div className="border-t" />
        </>
      )}

      {/* Search input */}
      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <Input
          placeholder={t("exercise_search_placeholder")}
          value={search}
          onChange={e => setSearch(e.target.value)}
          className="pl-9"
        />
      </div>

      {/* Loading skeletons */}
      {isSearching && debouncedSearch && (
        <div className="space-y-2">
          {Array.from({ length: 3 }).map((_, i) => (
            <div key={i} className="flex items-center gap-3">
              <Skeleton className="size-4 rounded-[4px]" />
              <Skeleton className="h-4 w-full rounded" />
            </div>
          ))}
        </div>
      )}

      {/* Search results (only unselected — selected ones are shown above) */}
      {!isSearching && unselectedResults.map(e => (
        <TriStateCheckbox
          key={e.id}
          label={e.title}
          count={e.wodCount}
          state="none"
          onToggle={() => onToggle(e.id)}
        />
      ))}

      {/* No results */}
      {!isSearching && debouncedSearch && searchResults.length === 0 && (
        <p className="text-muted-foreground text-xs">{t("exercise_no_results")}</p>
      )}

    </div>
  )
}

// ---------------------------------------------------------------------------
// TriStateCheckbox
// ---------------------------------------------------------------------------

function TriStateCheckbox({ label, count, state, onToggle }: {
  label: string
  count?: number
  state: OptionState
  onToggle: () => void
}) {
  const id = `filter-${label}`
  return (
    <div className="flex items-center gap-3">
      <Checkbox id={id} state={state} onToggle={onToggle} />
      <label
        htmlFor={id}
        className={`flex flex-1 cursor-pointer items-center justify-between text-sm font-medium ${state === "exclude" ? "text-destructive line-through" : ""}`}
      >
        {label}
        {count !== undefined && (
          <Badge variant="secondary" className="text-xs font-normal ">{count}</Badge>
        )}
      </label>
    </div>
  )
}
