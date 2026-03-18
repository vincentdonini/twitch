import { Card, CardContent, CardHeader } from "@workspace/ui/components/card"
import { Separator } from "@workspace/ui/components/separator"
import { Skeleton } from "@workspace/ui/components/skeleton"

export function WodDetailSkeleton() {
  return (
    <div className="grid grid-cols-6 gap-6">

      {/* Sidebar skeleton */}
      <div className="hidden md:block col-span-2">
        <Card className="shadow-none">
          <CardHeader className="pb-3">
            <Skeleton className="h-4 w-24" />
          </CardHeader>
          <CardContent className="space-y-5">

            <div className="space-y-2">
              <Skeleton className="h-3 w-16" />
              <div className="flex gap-1.5">
                <Skeleton className="h-5 w-14 rounded-full" />
                <Skeleton className="h-5 w-20 rounded-full" />
              </div>
            </div>

            <div className="space-y-2">
              <Skeleton className="h-3 w-28" />
              <div className="space-y-1.5">
                {[65, 80, 55].map((w, i) => (
                  <div key={i} className="flex items-center gap-2">
                    <Skeleton className="size-1.5 rounded-full shrink-0" />
                    <Skeleton className="h-3.5" style={{ width: `${w}%` }} />
                  </div>
                ))}
              </div>
            </div>

            <div className="space-y-2">
              <Skeleton className="h-3 w-20" />
              <div className="space-y-1.5">
                {[70, 50, 85, 60, 75].map((w, i) => (
                  <div key={i} className="flex items-center gap-2">
                    <Skeleton className="size-1.5 rounded-full shrink-0" />
                    <Skeleton className="h-3.5" style={{ width: `${w}%` }} />
                  </div>
                ))}
              </div>
            </div>

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
      </div>

      {/* Main content skeleton */}
      <div className="col-span-6 md:col-span-4 space-y-10">

        {/* Header */}
        <div className="space-y-3">
          <Skeleton className="h-10 w-3/4" />
          <div className="flex gap-2 pt-1">
          <Skeleton className="h-5 w-20 rounded-full" />
            <Skeleton className="h-5 w-16 rounded-full" />
          </div>
          <div className="space-y-2 hidden md:block px-2">
            <Skeleton className="h-5 w-full" />
            <Skeleton className="h-5 w-4/5" />
          </div>
        </div>

        {/* WodPattern (tabs + exercise list) */}
        <div className="space-y-6">
          <div className="flex gap-6 border-b pb-0">
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
