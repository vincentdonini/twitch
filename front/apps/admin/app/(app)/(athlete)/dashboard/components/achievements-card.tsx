import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Progress } from "@workspace/ui/components/progress"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Zap, Flame, Trophy, ArrowRight } from "lucide-react"

const recent = [
  {
    icon: Flame,
    color: "text-orange-500 bg-orange-500/10",
    name: "On Fire",
    description: "5-day streak",
    date: "Today",
  },
  {
    icon: Trophy,
    color: "text-yellow-500 bg-yellow-500/10",
    name: "PR Beast",
    description: "3 PRs in one week",
    date: "Mar 25",
  },
  {
    icon: Zap,
    color: "text-violet-500 bg-violet-500/10",
    name: "Volume King",
    description: "10 000 kg in a week",
    date: "Mar 22",
  },
]

const next = {
  name: "Iron Streak",
  description: "Reach a 10-day streak",
  current: 5,
  target: 10,
}

export function AchievementsCard() {
  const pct = Math.round((next.current / next.target) * 100)

  return (
    <Card>
      <CardHeader className="flex flex-row items-start justify-between">
        <div>
          <CardTitle>Achievements</CardTitle>
          <CardDescription>Your latest badges</CardDescription>
        </div>
        <Button variant="ghost" size="sm" className="cursor-pointer text-muted-foreground gap-1 shrink-0 -mt-1">
          View all <ArrowRight className="size-3.5" />
        </Button>
      </CardHeader>
      <CardContent className="space-y-4">
        <ul className="space-y-3">
          {recent.map((a) => {
            const Icon = a.icon
            return (
              <li key={a.name} className="flex items-center gap-3">
                <div className={`rounded-lg p-2 shrink-0 ${a.color}`}>
                  <Icon className="size-4" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium">{a.name}</p>
                  <p className="text-xs text-muted-foreground">{a.description}</p>
                </div>
                <Badge color="secondary" className="text-xs shrink-0">{a.date}</Badge>
              </li>
            )
          })}
        </ul>

        <div className="rounded-lg border p-3 space-y-2">
          <div className="flex items-center justify-between text-sm">
            <span className="font-medium">Next: {next.name}</span>
            <span className="text-muted-foreground">{next.current}/{next.target} days</span>
          </div>
          <Progress value={pct} className="h-2" />
          <p className="text-xs text-muted-foreground">{next.description}</p>
        </div>
      </CardContent>
    </Card>
  )
}
