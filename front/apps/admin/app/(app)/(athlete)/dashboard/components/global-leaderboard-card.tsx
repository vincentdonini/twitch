import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Medal, ArrowRight } from "lucide-react"

const ME = "Alex D."

const entries = [
  { rank: 1,  name: "Thomas R.",  initials: "TR", score: "3:12", rx: true,  box: "Box Alpha" },
  { rank: 2,  name: "Lucas M.",   initials: "LM", score: "3:48", rx: true,  box: "CrossFit Nord" },
  { rank: 3,  name: "Camille B.", initials: "CB", score: "4:05", rx: true,  box: null },
  { rank: 14, name: "Alex D.",    initials: "AD", score: "4:32", rx: true,  box: null },
  { rank: 15, name: "Julie S.",   initials: "JS", score: "4:38", rx: false, box: "Box Alpha" },
]

const medalColor: Record<number, string> = {
  1: "text-yellow-500",
  2: "text-slate-400",
  3: "text-amber-600",
}

export function GlobalLeaderboardCard() {
  const meIndex = entries.findIndex((e) => e.name === ME)

  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div>
          <CardTitle>Global Leaderboard</CardTitle>
          <CardDescription>Fran — Today</CardDescription>
        </div>
        <Button variant="ghost" size="sm" className="cursor-pointer text-muted-foreground gap-1">
          Full board <ArrowRight className="size-3.5" />
        </Button>
      </CardHeader>
      <CardContent className="space-y-1">
        {entries.map((entry, i) => {
          const isMe = entry.name === ME
          const showEllipsis = i > 0 && entries[i - 1].rank < entry.rank - 1

          return (
            <div key={entry.rank}>
              {showEllipsis && (
                <p className="text-xs text-muted-foreground text-center py-1">• • •</p>
              )}
              <div
                className={`flex items-center gap-3 rounded-lg px-3 py-2 ${
                  isMe ? "bg-primary/10 border border-primary/20" : ""
                }`}
              >
                <div className="w-7 flex justify-center shrink-0">
                  {entry.rank <= 3 ? (
                    <Medal className={`size-4 ${medalColor[entry.rank]}`} />
                  ) : (
                    <span className="text-sm text-muted-foreground font-medium tabular-nums">
                      {entry.rank}
                    </span>
                  )}
                </div>
                <Avatar className="size-7 shrink-0">
                  <AvatarFallback className="text-xs">{entry.initials}</AvatarFallback>
                </Avatar>
                <div className="flex-1 min-w-0">
                  <p className={`text-sm truncate ${isMe ? "font-semibold" : ""}`}>
                    {entry.name}{isMe && <span className="text-xs text-muted-foreground ml-1">(you)</span>}
                  </p>
                  {entry.box && (
                    <p className="text-xs text-muted-foreground truncate">{entry.box}</p>
                  )}
                </div>
                <span className="text-sm font-mono font-medium shrink-0">{entry.score}</span>
                {entry.rx && <Badge variant="outline" className="text-xs shrink-0">Rx</Badge>}
              </div>
            </div>
          )
        })}
      </CardContent>
    </Card>
  )
}
