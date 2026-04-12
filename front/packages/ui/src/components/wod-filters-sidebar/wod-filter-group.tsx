"use client"

import { Button } from "@workspace/ui/components/button"
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@workspace/ui/components/collapsible"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { ChevronDown } from "lucide-react"
import type { ReactNode } from "react"

interface WodFilterGroupProps {
  title: string
  isLoading: boolean
  children: ReactNode
  variant?: "default" | "sub"
}

export function WodFilterGroup({ title, isLoading, children, variant = "default" }: WodFilterGroupProps) {
  return (
    <Collapsible defaultOpen>
      <div className="flex items-center justify-between">
        {variant === "sub"
          ? <p className="text-sm font-medium text-muted-foreground">{title}</p>
          : <h4 className="text-lg font-medium">{title}</h4>
        }
        <CollapsibleTrigger asChild>
          <Button variant="ghost" size={variant === "sub" ? "sm" : "icon"} className={variant === "sub" ? "h-6 w-6 p-0" : ""}>
            <ChevronDown className={variant === "sub" ? "h-4 w-4" : "h-5 w-5"} />
          </Button>
        </CollapsibleTrigger>
      </div>
      <CollapsibleContent className="space-y-3 pt-3">
        {isLoading
          ? Array.from({ length: 5 }).map((_, i) => (
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
