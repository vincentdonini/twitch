import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Progress } from "@workspace/ui/components/progress"
import { Badge } from "@workspace/ui/components/badge"

const goals = [
  {
    lift: "Back Squat",
    current: 122,
    target: 150,
    unit: "kg",
    deadline: "Jun 2026",
  },
  {
    lift: "Deadlift",
    current: 162,
    target: 180,
    unit: "kg",
    deadline: "Apr 2026",
  },
  {
    lift: "Snatch",
    current: 68,
    target: 80,
    unit: "kg",
    deadline: "Sep 2026",
  },
]

export function GoalsCard() {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Personal Goals</CardTitle>
      </CardHeader>
      <CardContent className="space-y-5">
        {goals.map((g) => {
          const pct = Math.round((g.current / g.target) * 100)
          return (
            <div key={g.lift} className="space-y-1.5">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                  <span className="text-sm font-medium">{g.lift}</span>
                  <Badge variant="outline" className="text-xs">{g.deadline}</Badge>
                </div>
                <span className="text-sm text-muted-foreground">
                  {g.current} / {g.target} {g.unit}
                </span>
              </div>
              <Progress value={pct} className="h-2" />
              <p className="text-xs text-muted-foreground text-right">{pct}% of goal</p>
            </div>
          )
        })}
      </CardContent>
    </Card>
  )
}
