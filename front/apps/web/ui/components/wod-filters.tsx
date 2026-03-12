"use client"

import { Funnel, Search, ChevronDown } from "lucide-react"

import { Button } from "@workspace/ui/components/button"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Input } from "@workspace/ui/components/input"
import { Checkbox } from "@workspace/ui/components/checkbox"
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@workspace/ui/components/collapsible"
import { Sheet, SheetContent, SheetTrigger } from "@workspace/ui/components/sheet"

export function WodFilters() {
  return (
    <section className="py-8 sm:py-16 lg:py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {/* Mobile filter button */}
        <div className="mb-4 md:hidden">
          <Sheet>
            <SheetTrigger asChild>
              <Button variant="outline" className="w-full">
                <Funnel className="mr-2 h-4 w-4" />
                Filter
              </Button>
            </SheetTrigger>

            <SheetContent side="left" className="w-80">
              <FiltersSidebar />
            </SheetContent>
          </Sheet>
        </div>

        <div className="grid grid-cols-6 gap-6">

          {/* Sidebar */}
          <div className="hidden md:block col-span-2">
            <FiltersSidebar />
          </div>

          {/* Product grid placeholder */}
          <div className="col-span-6 md:col-span-4 border border-dashed rounded-lg min-h-[400px]" />
        </div>
      </div>
    </section>
  )
}

function FiltersSidebar() {
  return (
    <Card className="shadow-none">
      <CardHeader>
        <CardTitle>Filters</CardTitle>

        <div className="relative mt-4">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search..." className="pl-9" />
        </div>
      </CardHeader>

      <CardContent className="space-y-6">

        {/* Product type */}
        <FilterGroup title="Product Type">
          <FilterCheckbox label="Jeans" />
          <FilterCheckbox label="Sportswear" />
          <FilterCheckbox label="Shoes" />
          <FilterCheckbox label="Nightwear" />
        </FilterGroup>

        {/* Size */}
        <FilterGroup title="Size">
          <FilterCheckbox label="S" />
          <FilterCheckbox label="M" />
          <FilterCheckbox label="L" />
          <FilterCheckbox label="XL" />
        </FilterGroup>

      </CardContent>
    </Card>
  )
}

function FilterGroup({
                       title,
                       children,
                     }: {
  title: string
  children: React.ReactNode
}) {
  return (
    <Collapsible defaultOpen className="border-b pb-6">
      <div className="flex items-center justify-between">
        <h4 className="text-lg font-medium">{title}</h4>

        <CollapsibleTrigger asChild>
          <Button variant="ghost" size="icon">
            <ChevronDown className="h-5 w-5" />
          </Button>
        </CollapsibleTrigger>
      </div>

      <CollapsibleContent className="space-y-3 pt-4">
        {children}
      </CollapsibleContent>
    </Collapsible>
  )
}

function FilterCheckbox({ label }: { label: string }) {
  return (
    <div className="flex items-center gap-3">
      <Checkbox id={label} />
      <label htmlFor={label} className="text-sm font-medium">
        {label}
      </label>
    </div>
  )
}