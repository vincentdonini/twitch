import * as React from "react"
import * as TabsPrimitive from "@radix-ui/react-tabs"
import { cva } from "class-variance-authority"

import { cn } from "@workspace/ui/lib/utils"

type TabsVariant = "default" | "line"
const TabsVariantContext = React.createContext<TabsVariant>("default")

const tabsListVariants = cva(
  "inline-flex items-center justify-start",
  {
    variants: {
      variant: {
        default: "bg-muted text-muted-foreground h-9 w-fit rounded-lg p-[3px]",
        line: "text-muted-foreground h-auto w-fit gap-0 border-b border-border rounded-none p-0",
      },
    },
    defaultVariants: { variant: "default" },
  },
)

const tabsTriggerVariants = cva(
  "inline-flex items-center justify-center gap-1.5 text-sm font-medium whitespace-nowrap transition-[color] focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
  {
    variants: {
      variant: {
        default: "data-[state=active]:bg-primary data-[state=active]:text-primary-foreground dark:data-[state=active]:text-foreground focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:outline-ring dark:data-[state=active]:border-input dark:data-[state=active]:bg-input/30 text-foreground dark:text-muted-foreground h-[calc(100%-1px)] flex-1 rounded-md border border-transparent px-2 py-1 data-[state=active]:shadow-sm focus-visible:ring-[3px] focus-visible:outline-1",
        line: "data-[state=active]:text-foreground data-[state=active]:border-b-2 data-[state=active]:border-primary text-muted-foreground hover:text-foreground border-b-2 border-transparent px-4 py-2 -mb-px rounded-none",
      },
    },
    defaultVariants: { variant: "default" },
  },
)

function Tabs(
  {
    className,
    ...props
  }: React.ComponentProps<typeof TabsPrimitive.Root>,
) {
  return (
    <TabsPrimitive.Root
      data-slot="tabs"
      className={cn("flex flex-col gap-2", className)}
      {...props}
    />
  )
}

function TabsList(
  {
    className,
    variant = "default",
    ...props
  }: React.ComponentProps<typeof TabsPrimitive.List> & { variant?: TabsVariant },
) {
  return (
    <TabsVariantContext.Provider value={variant}>
      <TabsPrimitive.List
        data-slot="tabs-list"
        className={cn(tabsListVariants({ variant }), className)}
        {...props}
      />
    </TabsVariantContext.Provider>
  )
}

function TabsTrigger(
  {
    className,
    ...props
  }: React.ComponentProps<typeof TabsPrimitive.Trigger>,
) {
  const variant = React.useContext(TabsVariantContext)
  return (
    <TabsPrimitive.Trigger
      data-slot="tabs-trigger"
      className={cn(tabsTriggerVariants({ variant }), className)}
      {...props}
    />
  )
}

function TabsContent(
  {
    className,
    ...props
  }: React.ComponentProps<typeof TabsPrimitive.Content>,
) {
  return (
    <TabsPrimitive.Content
      data-slot="tabs-content"
      className={cn("flex-1 outline-none", className)}
      {...props}
    />
  )
}

export { Tabs, TabsList, TabsTrigger, TabsContent }
