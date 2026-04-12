"use client"

import { Card, CardContent } from "@workspace/ui/components/card"
import { useGetPlaceSubscriptions } from "@workspace/api"
import { UserCheck, Users } from "lucide-react"
import { CreateMemberDrawer } from "./create-member-drawer"
import { MembersTable } from "./members-table"

interface MembersViewProps {
  gymId: string
}

export function MembersView({ gymId }: MembersViewProps) {
  const { data, isLoading, error, refetch } = useGetPlaceSubscriptions(gymId)
  const subscriptions = data ?? []

  if (isLoading) {
    return (
      <div className="flex items-center justify-center py-16 text-muted-foreground">
        Chargement des membres...
      </div>
    )
  }

  if (error) {
    return (
      <div className="flex items-center justify-center py-16 text-destructive">
        Impossible de charger les membres.
      </div>
    )
  }

  const activeCount = subscriptions.filter((s) => s.status === "ACTIVE").length

  return (
    <div className="flex flex-col gap-6">
      <div className="flex items-start justify-between">
        <div className="grid grid-cols-2 gap-4">
          <Card className="border">
            <CardContent className="flex items-center gap-4">
              <Users className="size-8 text-muted-foreground" />
              <div>
                <p className="text-sm text-muted-foreground font-medium">Total abonnements</p>
                <p className="text-2xl font-bold">{subscriptions.length}</p>
              </div>
            </CardContent>
          </Card>
          <Card className="border">
            <CardContent className="flex items-center gap-4">
              <UserCheck className="size-8 text-green-500" />
              <div>
                <p className="text-sm text-muted-foreground font-medium">Actifs</p>
                <p className="text-2xl font-bold">{activeCount}</p>
              </div>
            </CardContent>
          </Card>
        </div>
        <CreateMemberDrawer gymId={gymId} onCreated={refetch} />
      </div>

      <MembersTable gymId={gymId} subscriptions={subscriptions} />
    </div>
  )
}
