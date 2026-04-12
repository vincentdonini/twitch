import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { ArrowRight, Medal } from "lucide-react"

const ME = "Alex D."

const leaderboard = [
  { rank: 1, name: "Thomas R.", initials: "TR", score: "18:32", rx: true },
  { rank: 2, name: "Lucas M.",  initials: "LM", score: "19:15", rx: true },
  { rank: 3, name: "Alex D.",   initials: "AD", score: "20:01", rx: true },
  { rank: 4, name: "Camille B.", initials: "CB", score: "21:44", rx: false },
  { rank: 5, name: "Julie S.",  initials: "JS", score: "22:10", rx: false },
]

const medalColor: Record<number, string> = {
  1: "text-yellow-500",
  2: "text-slate-400",
  3: "text-amber-600",
}

export function LeaderboardCard() {
  return (
    <Card>
      <CardHeader className="flex flex-row items-center justify-between">
        <div>
          <CardTitle>WOD Leaderboard</CardTitle>
          <CardDescription>Fran — Mar 28</CardDescription>
        </div>
        <Button variant="ghost" size="sm" className="cursor-pointer text-muted-foreground gap-1">
          Full board <ArrowRight className="size-3.5" />
        </Button>
      </CardHeader>
      <CardContent>
        <ul className="space-y-2">
          {leaderboard.map((entry) => {
            const isMe = entry.name === ME
            return (
              <li
                key={entry.rank}
                className={`flex items-center gap-3 rounded-lg px-3 py-2 ${
                  isMe ? "bg-primary/10 border border-primary/20" : ""
                }`}
              >
                <div className="w-6 flex justify-center shrink-0">
                  {entry.rank <= 3 ? (
                    <Medal className={`size-4 ${medalColor[entry.rank]}`} />
                  ) : (
                    <span className="text-sm text-muted-foreground font-medium">{entry.rank}</span>
                  )}
                </div>
                <Avatar className="size-7 shrink-0">
                  <AvatarFallback className="text-xs">{entry.initials}</AvatarFallback>
                </Avatar>
                <p className={`flex-1 text-sm truncate ${isMe ? "font-semibold" : ""}`}>
                  {entry.name} {isMe && <span className="text-xs text-muted-foreground">(you)</span>}
                </p>
                <span className="text-sm font-mono font-medium shrink-0">{entry.score}</span>
                {entry.rx && (
                  <Badge variant="outline" className="text-xs shrink-0">Rx</Badge>
                )}
              </li>
            )
          })}
        </ul>
      </CardContent>
    </Card>
  )
}
