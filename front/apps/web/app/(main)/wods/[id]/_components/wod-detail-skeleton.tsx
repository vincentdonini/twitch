import { Card, CardContent, CardHeader } from "@workspace/ui/components/card"
import { Separator } from "@workspace/ui/components/separator"
import { Skeleton } from "@workspace/ui/components/skeleton"

function StatsSkeleton() {
  return (
    <Card className="shadow-none order-2 md:order-1">
      <CardHeader className="pb-3">
        <Skeleton className="h-4 w-24" />
      </CardHeader>
      <CardContent className="space-y-5">

        {/* Divisions — badges */}
        <div className="space-y-2">
          <Skeleton className="h-3 w-16" />
          <div className="flex flex-wrap gap-1.5">
            <Skeleton className="h-5 w-14 rounded-full" />
            <Skeleton className="h-5 w-20 rounded-full" />
          </div>
        </div>

        {/* Exercise categories — bullet list */}
        <div className="space-y-2">
          <Skeleton className="h-3 w-32" />
          <div className="space-y-1.5">
            {[65, 80, 55].map((w, i) => (
              <div key={i} className="flex items-center gap-2">
                <Skeleton className="size-1.5 rounded-full shrink-0" />
                <Skeleton className="h-3.5" style={{ width: `${w}%` }} />
              </div>
            ))}
          </div>
        </div>

        {/* Exercises — bullet list */}
        <div className="space-y-2">
          <Skeleton className="h-3 w-24" />
          <div className="space-y-1.5">
            {[70, 50, 85, 60, 75].map((w, i) => (
              <div key={i} className="flex items-center gap-2">
                <Skeleton className="size-1.5 rounded-full shrink-0" />
                <Skeleton className="h-3.5" style={{ width: `${w}%` }} />
              </div>
            ))}
          </div>
        </div>

        {/* Equipment — bullet list */}
        <div className="space-y-2">
          <Skeleton className="h-3 w-20" />
          <div className="space-y-1.5">
            {[55, 75].map((w, i) => (
              <div key={i} className="flex items-center gap-2">
                <Skeleton className="size-1.5 rounded-full shrink-0" />
                <Skeleton className="h-3.5" style={{ width: `${w}%` }} />
              </div>
            ))}
          </div>
        </div>

      </CardContent>
    </Card>
  )
}

function LeaderboardSkeleton() {
  return (
    <Card className="shadow-none order-1 md:order-2">
      <CardHeader className="pb-3">
        <Skeleton className="h-4 w-28" />
      </CardHeader>
      <CardContent className="px-0 pb-4 space-y-3">

        {/* Tabs divisions */}
        <div className="px-4">
          <Skeleton className="h-9 w-full rounded-md" />
        </div>

        {/* 1st place podium */}
        <div className="mx-4 rounded-xl overflow-hidden">
          <Skeleton className="h-[72px] w-full" />
        </div>

        {/* Rows 2–5 */}
        <div className="px-4 space-y-0">
          {[40, 70, 55, 65].map((w, i) => (
            <div key={i} className="flex items-center gap-2 py-2 border-b border-border/40 last:border-0">
              <Skeleton className="h-3 w-4 shrink-0" />
              <Skeleton className="size-7 rounded-full shrink-0" />
              <Skeleton className="h-3.5 flex-1" style={{ maxWidth: `${w}%` }} />
              <Skeleton className="h-3.5 w-14 shrink-0 ml-auto" />
            </div>
          ))}
        </div>

      </CardContent>
    </Card>
  )
}

export function WodDetailSkeleton() {
  return (
    <div className="grid grid-cols-1 lg:grid-cols-6 gap-6">

      {/* Sidebar */}
      <div className="order-2 lg:order-1 col-span-2">
        <div className="grid grid-cols-1 gap-6">
          <StatsSkeleton />
          <LeaderboardSkeleton />
        </div>
      </div>

      {/* Main content */}
      <div className="order-1 lg:order-2 col-span-1 md:col-span-4 space-y-10">

        {/* Header */}
        <div className="space-y-3">
          <Skeleton className="h-10 w-3/4" />
          <div className="flex gap-2 pt-1">
            <Skeleton className="h-5 w-20 rounded-full" />
            <Skeleton className="h-5 w-16 rounded-full" />
          </div>
          <div className="hidden md:space-y-2 md:block px-2">
            <Skeleton className="h-5 w-full" />
            <Skeleton className="h-5 w-4/5" />
          </div>
        </div>

        {/* WodPattern */}
        <div className="space-y-6">
          <div className="flex gap-2 border-b pb-1">
            <Skeleton className="h-8 w-20" />
            <Skeleton className="h-8 w-20" />
          </div>
          <div className="space-y-3 pt-2">
            {[55, 45, 70, 40, 60].map((w, i) => (
              <Skeleton key={i} className="h-7" style={{ width: `${w}%` }} />
            ))}
          </div>
        </div>

        <Separator />

        {/* Details section */}
        <div className="space-y-3">
          <Skeleton className="h-6 w-24" />
          <div className="space-y-2">
            {[100, 92, 80, 100, 75].map((w, i) => (
              <Skeleton key={i} className="h-4" style={{ width: `${w}%` }} />
            ))}
          </div>
        </div>

      </div>
    </div>
  )
}
