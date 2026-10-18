import { Card, CardContent } from "@workspace/ui/components/card"
import { Dumbbell, Flame, CalendarCheck, TrendingUp } from "lucide-react"

export function SessionsKpi() {
  return (
    <div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
      <Card>
        <CardContent className="flex items-start gap-3 pt-5">
          <div className="rounded-lg p-2 shrink-0 bg-blue-500/10 text-blue-500">
            <Dumbbell className="size-4" />
          </div>
          <div className="min-w-0">
            <p className="text-xs text-muted-foreground">This month</p>
            <p className="text-2xl font-bold leading-tight">12</p>
            <p className="text-xs text-muted-foreground mt-0.5">sessions</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="flex items-start gap-3 pt-5">
          <div className="rounded-lg p-2 shrink-0 bg-orange-500/10 text-orange-500">
            <Flame className="size-4" />
          </div>
          <div className="min-w-0">
            <p className="text-xs text-muted-foreground">Current streak</p>
            <p className="text-2xl font-bold leading-tight">5</p>
            <p className="text-xs text-muted-foreground mt-0.5">days in a row</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="flex items-start gap-3 pt-5">
          <div className="rounded-lg p-2 shrink-0 bg-green-500/10 text-green-500">
            <CalendarCheck className="size-4" />
          </div>
          <div className="min-w-0">
            <p className="text-xs text-muted-foreground">This year</p>
            <p className="text-2xl font-bold leading-tight">47</p>
            <p className="text-xs text-muted-foreground mt-0.5">sessions total</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardContent className="flex items-start gap-3 pt-5">
          <div className="rounded-lg p-2 shrink-0 bg-purple-500/10 text-purple-500">
            <TrendingUp className="size-4" />
          </div>
          <div className="min-w-0">
            <p className="text-xs text-muted-foreground">Weekly avg</p>
            <p className="text-2xl font-bold leading-tight">3.4</p>
            <p className="text-xs text-muted-foreground mt-0.5">sessions / week</p>
          </div>
        </CardContent>
      </Card>
    </div>
  )
}
