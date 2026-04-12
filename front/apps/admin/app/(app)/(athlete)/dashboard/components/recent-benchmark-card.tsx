import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { TrendingDown, TrendingUp, Minus, ArrowRight } from "lucide-react"

const benchmarks = [
  {
    name: "1RM Deadlift",
    category: "Strength",
    score: "172 kg",
    prev: "162 kg",
    unit: "weight",
    date: "Mar 27",
  },
  {
    name: "3RM Back Squat",
    category: "Strength",
    score: "122 kg",
    prev: "122 kg",
    unit: "weight",
    date: "Mar 22",
  },
  {
    name: "Max Pull-ups",
    category: "Gymnastics",
    score: "34 reps",
    prev: "29 reps",
    unit: "reps",
    date: "Mar 15",
  },
  {
    name: "Row 1K",
    category: "Cardio",
    score: "3:24",
    prev: "3:31",
    unit: "time",
    date: "Mar 10",
  },
]

const categoryVariant: Record<string, { variant?: "outline"; color?: "default" | "secondary" }> = {
  Strength: { color: "secondary" },
  Gymnastics: { variant: "outline" },
  Cardio: { color: "default" },
}

function Trend({ score, prev, unit }: { score: string; prev: string | null; unit: string }) {
  if (!prev) return <span className="text-xs text-muted-foreground">First</span>

  const isTime = unit === "time"
  const improved = isTime ? score < prev : score > prev
  const same = score === prev

  if (same) return <Minus className="size-3.5 text-muted-foreground" />
  if (improved) return <TrendingUp className="size-3.5 text-emerald-500" />
  return <TrendingDown className="size-3.5 text-rose-500" />
}

export function RecentBenchmarkCard() {
  return (
    <Card>
      <CardHeader className="flex flex-row items-start justify-between">
        <div>
          <CardTitle>Recent Benchmarks</CardTitle>
          <CardDescription>Your latest PRs on key exercises</CardDescription>
        </div>
        <Button variant="ghost" size="sm" className="cursor-pointer text-muted-foreground gap-1 shrink-0 -mt-1">
          View all <ArrowRight className="size-3.5" />
        </Button>
      </CardHeader>
      <CardContent className="space-y-2">
        {benchmarks.map((b) => (
          <div
            key={b.name}
            className="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-muted/50 transition-colors"
          >
            <div className="flex-1 min-w-0">
              <div className="flex items-center gap-2">
                <p className="text-sm font-medium truncate">{b.name}</p>
                <Badge {...(categoryVariant[b.category] ?? { variant: "outline" })} className="text-xs shrink-0">
                  {b.category}
                </Badge>
              </div>
              <p className="text-xs text-muted-foreground mt-0.5">{b.date}</p>
            </div>
            <div className="flex items-center gap-2 shrink-0">
              <Trend score={b.score} prev={b.prev} unit={b.unit} />
              <span className="text-sm font-mono font-semibold tabular-nums">{b.score}</span>
            </div>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}
