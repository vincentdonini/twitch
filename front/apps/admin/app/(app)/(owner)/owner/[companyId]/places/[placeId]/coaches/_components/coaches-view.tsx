"use client"

import { Card, CardContent } from "@workspace/ui/components/card"
import { useGetPlaceCoaches } from "@workspace/api"
import { UserCheck } from "lucide-react"
import { CoachesTable } from "./coaches-table"

interface CoachesViewProps {
  placeId: string
}

export function CoachesView({ placeId }: CoachesViewProps) {
  const { data, isLoading, error, refetch } = useGetPlaceCoaches(placeId)
  const coaches = data ?? []

  if (isLoading) {
    return (
      <div className="flex items-center justify-center py-16 text-muted-foreground">
        Chargement des coachs...
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex items-center justify-center py-16 text-destructive">
        Impossible de charger les coachs.
      </div>
    )
  }

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-start justify-between">
        <Card className="border">
          <CardContent className="flex items-center gap-4">
            <UserCheck className="size-8 text-muted-foreground" />
            <div>
              <p className="text-sm text-muted-foreground font-medium">Total coachs</p>
              <p className="text-2xl font-bold">{coaches.length}</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <CoachesTable placeId={placeId} coaches={coaches} onRemove={refetch} />
    </div>
  )
}
