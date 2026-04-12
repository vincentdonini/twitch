"use client"

import { Button } from "@workspace/ui/components/button"
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@workspace/ui/components/collapsible"
import { Stack } from "@workspace/ui/components/stack"
import { ChevronDown } from "lucide-react"
import type { FilterGroupSection } from "./types"
import { WodFilterGroup } from "./wod-filter-group"
import { WodTriStateCheckbox } from "./wod-tri-state-checkbox"

interface WodFilterSectionProps {
  section: FilterGroupSection
}

export function WodFilterSection({ section }: WodFilterSectionProps) {
  return (
    <Collapsible defaultOpen>
      <div className="flex items-center justify-between">
        <h4 className="text-lg font-medium">{section.title}</h4>
        <CollapsibleTrigger asChild>
          <Button variant="ghost" size="icon">
            <ChevronDown className="h-5 w-5" />
          </Button>
        </CollapsibleTrigger>
      </div>
      <CollapsibleContent>
        <Stack gap={4} divider className="pt-4">
          {section.groups.map(group => (
            <WodFilterGroup key={group.id} title={group.title} isLoading={group.isLoading} variant="sub">
              {group.renderContent
                ? group.renderContent()
                : (group.options ?? []).map(option => (
                  <WodTriStateCheckbox
                    key={option.id}
                    label={option.label}
                    count={option.count}
                    state={group.getState(option.id)}
                    onToggle={() => group.onToggle(option.id)}
                  />
                ))
              }
            </WodFilterGroup>
          ))}
        </Stack>
      </CollapsibleContent>
    </Collapsible>
  )
}
