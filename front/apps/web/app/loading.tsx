import { Skeleton } from "@workspace/ui/components/skeleton"

export default function Loading() {
  return (
    <section className="py-12 sm:py-24 bg-muted/50 min-h-[calc(100vh-65px)]">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-2xl text-center mb-16 space-y-4">
          <Skeleton className="h-6 w-24 mx-auto rounded-full" />
          <Skeleton className="h-10 w-2/3 mx-auto" />
          <Skeleton className="h-5 w-3/4 mx-auto" />
        </div>
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, i) => (
            <Skeleton key={i} className="h-48 w-full rounded-xl" />
          ))}
        </div>
      </div>
    </section>
  )
}
