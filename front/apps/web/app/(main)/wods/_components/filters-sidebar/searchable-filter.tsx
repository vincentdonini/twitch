"use client"

import { OptionState } from "@/app/(main)/wods/_lib/filters"
import { Input } from "@workspace/ui/components/input"
import { ScrollArea } from "@workspace/ui/components/scroll-area"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { Search } from "lucide-react"
import { useState } from "react"
import { TriStateCheckbox } from "./tri-state-checkbox"

export interface SearchableFilterItem {
  id: string
  title: string
  wodCount?: number
}

interface SearchableFilterProps {
  items: SearchableFilterItem[]
  isLoading: boolean
  selectedIds: { include: string[]; exclude: string[] }
  getState: (id: string) => OptionState
  onToggle: (id: string) => void
  placeholder: string
  noResultsText: string
}

export function SearchableFilter({
  items,
  isLoading,
  selectedIds,
  getState,
  onToggle,
  placeholder,
  noResultsText,
}: SearchableFilterProps) {
  const [search, setSearch] = useState("")

  const allSelectedIds = [...new Set([...selectedIds.include, ...selectedIds.exclude])]
  const filtered = items.filter(e => !search || e.title.toLowerCase().includes(search.toLowerCase()))
  const unselected = filtered.filter(e => getState(e.id) === "none")

  return (
    <div className="space-y-3">
      {allSelectedIds.length > 0 && (
        <>
          <div className="space-y-2">
            {allSelectedIds.map(id => {
              const item = items.find(e => e.id === id)
              return (
                <TriStateCheckbox
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
              <TriStateCheckbox
                key={e.id}
                label={e.title}
                count={e.wodCount}
                state="none"
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
