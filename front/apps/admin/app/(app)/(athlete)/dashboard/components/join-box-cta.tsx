import { Card, CardContent } from "@workspace/ui/components/card"
import { Button } from "@workspace/ui/components/button"
import { Building2, CalendarCheck, Users, TrendingUp } from "lucide-react"

const perks = [
  { icon: CalendarCheck, label: "Access to group sessions & planning" },
  { icon: Users,         label: "Train with a community" },
  { icon: TrendingUp,    label: "Personalized coaching & follow-up" },
]

export function JoinBoxCta() {
  return (
    <Card className="border-dashed">
      <CardContent className="flex flex-col items-center gap-4 py-8 text-center">
        <div className="rounded-full bg-muted p-4">
          <Building2 className="size-8 text-muted-foreground" />
        </div>
        <div>
          <p className="font-semibold text-lg">Join a box</p>
          <p className="text-sm text-muted-foreground mt-1 max-w-xs">
            Connect with a gym to unlock sessions, planning, coaching and box leaderboards.
          </p>
        </div>
        <ul className="flex flex-col gap-2 text-left w-full max-w-xs">
          {perks.map((perk) => {
            const Icon = perk.icon
            return (
              <li key={perk.label} className="flex items-center gap-2 text-sm text-muted-foreground">
                <Icon className="size-4 shrink-0 text-primary" />
                {perk.label}
              </li>
            )
          })}
        </ul>
        <Button className="cursor-pointer w-full max-w-xs">Find a box</Button>
      </CardContent>
    </Card>
  )
}
