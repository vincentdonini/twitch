"use client"

import { Input } from "@workspace/ui/components/input"
import { ScrollArea } from "@workspace/ui/components/scroll-area"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { Search } from "lucide-react"
import { useState } from "react"
import { OptionState } from "./types"
import { WodTriStateCheckbox } from "./wod-tri-state-checkbox"

export interface WodSearchableFilterItem {
  id: string
  title: string
  wodCount?: number
}

interface WodSearchableFilterProps {
  items: WodSearchableFilterItem[]
  isLoading: boolean
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  placeholder: string
  noResultsText: string
}

export function WodSearchableFilter(
  {
    items,
    isLoading,
    selectedIds,
    getState,
    onToggle,
    placeholder,
    noResultsText,
  }: WodSearchableFilterProps,
) {
  const [search, setSearch] = useState("")
  const safeItems = items ?? []
  const allSelectedIds = [...new Set([...selectedIds.include, ...selectedIds.exclude])]
  const filtered = safeItems.filter(e => !search || e.title.toLowerCase().includes(search.toLowerCase()))
  const unselected = filtered.filter(e => getState(e.id) === OptionState.None)

  return (
    <div className="space-y-3">
      {allSelectedIds.length > 0 && (
        <>
          <div className="space-y-2">
            {allSelectedIds.map(id => {
              const item = safeItems.find(e => e.id === id)
              return (
                <WodTriStateCheckbox
                  key={id}
                  label={item?.title ?? `${id.substring(0, 8)}…`}
                  count={item?.wodCount}
                  state={getState(id)}
                  onToggle={() => onToggle(id)}
                />
              )
            })}
          </div>
          <div className="border-t" />
        </>
      )}

      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
        <Input
          placeholder={placeholder}
          value={search}
          onChange={e => setSearch(e.target.value)}
          className="pl-9"
        />
      </div>

      {isLoading ? (
        <div className="space-y-2">
          {Array.from({ length: 4 }).map((_, i) => (
            <div key={i} className="flex items-center gap-3">
              <Skeleton className="size-4 rounded-[4px]" />
              <Skeleton className="h-4 w-full rounded" />
            </div>
          ))}
        </div>
      ) : (
        <ScrollArea className="max-h-36 overflow-y-auto">
          <div className="space-y-2 pr-3">
            {unselected.map(e => (
              <WodTriStateCheckbox
                key={e.id}
                label={e.title}
                count={e.wodCount}
                state={OptionState.None}
                onToggle={() => onToggle(e.id)}
              />
            ))}
            {unselected.length === 0 && (
              <p className="text-muted-foreground text-xs py-2">{noResultsText}</p>
            )}
          </div>
        </ScrollArea>
      )}
    </div>
  )
}
