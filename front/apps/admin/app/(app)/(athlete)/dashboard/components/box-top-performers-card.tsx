import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Progress } from "@workspace/ui/components/progress"
import { Medal } from "lucide-react"

const ME = "Alex D."

const performers = [
  { rank: 1, name: "Thomas R.",  initials: "TR", sessions: 18, target: 20 },
  { rank: 2, name: "Camille B.", initials: "CB", sessions: 16, target: 20 },
  { rank: 3, name: "Julie S.",   initials: "JS", sessions: 15, target: 20 },
  { rank: 4, name: "Alex D.",    initials: "AD", sessions: 12, target: 20 },
  { rank: 5, name: "Lucas M.",   initials: "LM", sessions: 10, target: 20 },
]

const medalColor: Record<number, string> = {
  1: "text-yellow-500",
  2: "text-slate-400",
  3: "text-amber-600",
}

export function BoxTopPerformersCard() {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Box Top Performers</CardTitle>
        <CardDescription>Sessions completed — March</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        {performers.map((p) => {
          const isMe = p.name === ME
          const pct = Math.round((p.sessions / p.target) * 100)

          return (
            <div
              key={p.rank}
              className={`flex items-center gap-3 rounded-lg p-2 ${
                isMe ? "bg-primary/10 border border-primary/20" : ""
              }`}
            >
              <div className="w-6 flex justify-center shrink-0">
                {p.rank <= 3 ? (
                  <Medal className={`size-4 ${medalColor[p.rank]}`} />
                ) : (
                  <span className="text-sm text-muted-foreground font-medium">{p.rank}</span>
                )}
              </div>
              <Avatar className="size-7 shrink-0">
                <AvatarFallback className="text-xs">{p.initials}</AvatarFallback>
              </Avatar>
              <div className="flex-1 min-w-0 space-y-1">
                <div className="flex items-center justify-between gap-2">
                  <p className={`text-sm truncate ${isMe ? "font-semibold" : ""}`}>
                    {p.name}{isMe && <span className="text-xs text-muted-foreground ml-1">(you)</span>}
                  </p>
                  <span className="text-xs text-muted-foreground shrink-0 tabular-nums">
                    {p.sessions}/{p.target}
                  </span>
                </div>
                <Progress value={pct} className="h-1.5" />
              </div>
            </div>
          )
        })}
      </CardContent>
    </Card>
  )
}
