"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Separator } from "@workspace/ui/components/separator"
import { CalendarDays, Medal, ArrowRight, Pencil } from "lucide-react"

const wod = {
  name: "Fran",
  type: "For Time",
  description: "21-15-9\nThrusters (43 / 30 kg)\nPull-ups",
  timeCapMinutes: 10,
  date: "Today — Mar 28",
}

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

export function WodWithLeaderboard() {
  const [logging, setLogging] = useState(false)
  const [score, setScore] = useState("")

  return (
    <Card>
      <div className="grid grid-cols-1 lg:grid-cols-5 divide-y lg:divide-y-0 lg:divide-x">

        {/* WOD — 3 cols */}
        <div className="lg:col-span-3 flex flex-col">
          <CardHeader>
            <div className="flex items-start justify-between gap-2">
              <div>
                <CardTitle className="flex items-center gap-2">
                  <CalendarDays className="size-4 text-muted-foreground" />
                  WOD of the Day
                </CardTitle>
                <CardDescription>{wod.date}</CardDescription>
              </div>
              <Badge variant="outline">{wod.type}</Badge>
            </div>
          </CardHeader>
          <CardContent className="flex flex-col gap-4 flex-1">
            <div>
              <p className="text-2xl font-bold">{wod.name}</p>
              <pre className="text-sm text-muted-foreground mt-1 whitespace-pre-wrap font-sans leading-relaxed">
                {wod.description}
              </pre>
              <p className="text-xs text-muted-foreground mt-2">
                Time cap: {wod.timeCapMinutes} min
              </p>
            </div>

            {!logging ? (
              <Button
                className="w-full cursor-pointer mt-auto"
                onClick={() => setLogging(true)}
              >
                <Pencil className="size-4 mr-2" />
                Log my score
              </Button>
            ) : (
              <div className="flex gap-2 mt-auto">
                <Input
                  placeholder="e.g. 4:32 or 7 rounds"
                  value={score}
                  onChange={(e) => setScore(e.target.value)}
                />
                <Button className="cursor-pointer shrink-0" disabled={!score.trim()}>
                  Save
                </Button>
              </div>
            )}
          </CardContent>
        </div>

        {/* Leaderboard — 1 col */}
        <div className="flex flex-col col-span-2">
          <CardHeader className="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Leaderboard</CardTitle>
              <CardDescription>Fran — Today</CardDescription>
            </div>
            <Button variant="ghost" size="icon" className="cursor-pointer text-muted-foreground size-7">
              <ArrowRight className="size-4" />
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
                    className={`flex items-center gap-2 rounded-lg px-2 py-2 ${
                      isMe ? "bg-primary/10 border border-primary/20" : ""
                    }`}
                  >
                    <div className="w-6 flex justify-center shrink-0">
                      {entry.rank <= 3 ? (
                        <Medal className={`size-4 ${medalColor[entry.rank]}`} />
                      ) : (
                        <span className="text-sm text-muted-foreground font-medium tabular-nums">
                          {entry.rank}
                        </span>
                      )}
                    </div>
                    <Avatar className="size-6 shrink-0">
                      <AvatarFallback className="text-xs">{entry.initials}</AvatarFallback>
                    </Avatar>
                    <div className="flex-1 min-w-0">
                      <p className={`text-xs truncate ${isMe ? "font-semibold" : ""}`}>
                        {entry.name}
                        {isMe && <span className="text-muted-foreground ml-1">(you)</span>}
                      </p>
                    </div>
                    <div className="flex items-center gap-1 shrink-0">
                      <span className="text-xs font-mono font-medium">{entry.score}</span>
                      {entry.rx && <Badge variant="outline" className="text-xs px-1 py-0">Rx</Badge>}
                    </div>
                  </div>
                </div>
              )
            })}
          </CardContent>
        </div>

      </div>
    </Card>
  )
}
