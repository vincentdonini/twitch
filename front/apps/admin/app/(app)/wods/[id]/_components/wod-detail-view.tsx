"use client"

import { WodContent } from "@/app/(app)/wods/[id]/_components/wod-content"
import { type WodDetail } from "@workspace/api"
import { Button } from "@workspace/ui/components/button"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { ChevronLeft } from "lucide-react"
import { useTranslations } from "next-intl"
import Link from "next/link"

interface WodDetailViewProps {
  wod: WodDetail | null
  isLoading: boolean
  error: string | null
  onRetry: () => void
}

export function WodDetailView({ wod, isLoading, error, onRetry }: WodDetailViewProps) {
  const tc = useTranslations("common.wods")

  return (
    <div className="flex flex-col gap-6 px-4 lg:px-6">
      {/* Back link */}
      <div>
        <Button variant="ghost" size="sm" asChild className="-ml-2 cursor-pointer">
          <Link href="/wods">
            <ChevronLeft className="mr-1 size-4" />
            {tc("back")}
          </Link>
        </Button>
      </div>

      {/* Loading */}
      {isLoading && (
        <div className="space-y-6">
          <Skeleton className="h-10 w-56" />
          <Skeleton className="h-6 w-48" />
          <Skeleton className="h-9 w-64" />
          <Skeleton className="h-64 w-full" />
        </div>
      )}

      {/* Error */}
      {!isLoading && error && (
        <div className="flex flex-col items-center justify-center py-16 gap-4 text-center">
          <p className="text-destructive">{error}</p>
          <Button onClick={onRetry} variant="outline">{tc("back")}</Button>
        </div>
      )}

      {/* Content */}
      {!isLoading && !error && wod && (
        <WodContent wod={wod} />
      )}
    </div>
  )
}
