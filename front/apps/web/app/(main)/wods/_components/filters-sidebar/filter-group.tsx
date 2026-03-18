"use client"

import { Button } from "@workspace/ui/components/button"
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@workspace/ui/components/collapsible"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { ChevronDown } from "lucide-react"

interface FilterGroupProps {
  title: string
  isLoading: boolean
  children: React.ReactNode
}

export function FilterGroup({ title, isLoading, children }: FilterGroupProps) {
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
