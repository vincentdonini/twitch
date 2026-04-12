"use client"

import { Card, CardContent } from "@workspace/ui/components/card"
import { Input } from "@workspace/ui/components/input"
import { Stack } from "@workspace/ui/components/stack"
import { Search } from "lucide-react"
import { useTranslations } from "next-intl"
import { isFilterGroupSection } from "./types"
import type { FilterItem } from "./types"
import { WodFilterGroup } from "./wod-filter-group"
import { WodFilterSection } from "./wod-filter-section"
import { WodTriStateCheckbox } from "./wod-tri-state-checkbox"

interface WodFiltersSidebarProps {
  filterGroups: FilterItem[]
  searchQuery: string
  onSearchChange: (q: string) => void
}

export function WodFiltersSidebar(
  {
    filterGroups,
    searchQuery,
    onSearchChange,
  }: WodFiltersSidebarProps,
) {
  const tc = useTranslations("common.wods")

  return (
    <Card className="shadow-none bg-transparent border-0 m-0 p-0">
      <CardContent className="p-0">
        <Stack gap={6} divider>
          <div className="relative mt-4">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder={tc("search_placeholder")}
              value={searchQuery}
              onChange={e => onSearchChange(e.target.value)}
              className="pl-9"
            />
          </div>

          {filterGroups.map(item =>
            isFilterGroupSection(item)
              ? <WodFilterSection key={item.id} section={item} />
              : (
                <WodFilterGroup key={item.id} title={item.title} isLoading={item.isLoading}>
                  {item.renderContent
                    ? item.renderContent()
                    : (item.options ?? []).map(option => (
                      <WodTriStateCheckbox
                        key={option.id}
                        label={option.label}
                        count={option.count}
                        state={item.getState(option.id)}
                        onToggle={() => item.onToggle(option.id)}
                      />
                    ))
                  }
                </WodFilterGroup>
              )
          )}
        </Stack>
      </CardContent>
    </Card>
  )
}
