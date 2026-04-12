import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Trophy, ArrowRight } from "lucide-react"

const performances = [
  {
    date: "Mar 27",
    wod: "Cindy",
    score: "22 rounds",
    type: "AMRAP",
    pr: true,
  },
  {
    date: "Mar 26",
    wod: "Back Squat 5×5",
    score: "122 kg",
    type: "Strength",
    pr: false,
  },
  {
    date: "Mar 25",
    wod: "Grace",
    score: "2:48",
    type: "For Time",
    pr: false,
  },
  {
    date: "Mar 23",
    wod: "Deadlift 3RM",
    score: "162 kg",
    type: "Strength",
    pr: true,
  },
]

const typeVariant: Record<string, { variant?: "outline"; color?: "default" | "secondary" }> = {
  AMRAP: { color: "default" },
  Strength: { color: "secondary" },
  "For Time": { variant: "outline" },
}

export function RecentPerformances() {
  return (
    <Card>
      <CardHeader className="flex flex-row items-start justify-between">
        <div>
          <CardTitle>Recent Performances</CardTitle>
          <CardDescription>Your last 5 sessions</CardDescription>
        </div>
        <Button variant="ghost" size="sm" className="cursor-pointer text-muted-foreground gap-1 shrink-0 -mt-1">
          View all <ArrowRight className="size-3.5" />
        </Button>
      </CardHeader>
      <CardContent className="space-y-2">
        {performances.map((p) => (
          <div
            key={`${p.date}-${p.wod}`}
            className="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-muted/50 transition-colors"
          >
            <div className="flex-1 min-w-0">
              <div className="flex items-center gap-2">
                <p className="text-sm font-medium truncate">{p.wod}</p>
                {p.pr && <Trophy className="size-3.5 text-yellow-500 shrink-0" />}
                <Badge {...(typeVariant[p.type] ?? { variant: "outline" })} className="text-xs shrink-0">
                  {p.type}
                </Badge>
              </div>
              <p className="text-xs text-muted-foreground mt-0.5">{p.date}</p>
            </div>
            <span className="text-sm font-mono font-semibold tabular-nums shrink-0">{p.score}</span>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}
