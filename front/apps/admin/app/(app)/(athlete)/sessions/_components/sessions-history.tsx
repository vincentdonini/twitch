"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import { Trophy, Search, ChevronRight } from "lucide-react"
import { cn } from "@/lib/utils"

type SessionRecord = {
  id: string
  date: string
  wod: string
  type: "WOD" | "Strength" | "Open gym" | "Benchmark"
  score: string
  duration: string
  pr: boolean
}

const history: SessionRecord[] = [
  { id: "h01", date: "Apr 11", wod: "Murph",             type: "WOD",       score: "51:24",       duration: "55 min", pr: false },
  { id: "h02", date: "Apr 09", wod: "Back Squat 5×5",    type: "Strength",  score: "130 kg",      duration: "45 min", pr: false },
  { id: "h03", date: "Apr 08", wod: "Grace",             type: "Benchmark", score: "2:41",        duration: "20 min", pr: true  },
  { id: "h04", date: "Apr 07", wod: "Open gym",          type: "Open gym",  score: "—",           duration: "45 min", pr: false },
  { id: "h05", date: "Apr 05", wod: "Cindy",             type: "Benchmark", score: "23 rounds",   duration: "25 min", pr: true  },
  { id: "h06", date: "Apr 04", wod: "Deadlift 1RM",      type: "Strength",  score: "175 kg",      duration: "40 min", pr: false },
  { id: "h07", date: "Apr 02", wod: "Annie",             type: "Benchmark", score: "8:12",        duration: "20 min", pr: false },
  { id: "h08", date: "Apr 01", wod: "Snatch 3×3",        type: "Strength",  score: "72 kg",       duration: "50 min", pr: false },
  { id: "h09", date: "Mar 28", wod: "Fran",              type: "Benchmark", score: "3:58",        duration: "15 min", pr: false },
  { id: "h10", date: "Mar 27", wod: "Karen",             type: "Benchmark", score: "12:34",       duration: "20 min", pr: false },
  { id: "h11", date: "Mar 26", wod: "Clean & Jerk 3RM",  type: "Strength",  score: "95 kg",       duration: "45 min", pr: false },
  { id: "h12", date: "Mar 25", wod: "Isabel",            type: "Benchmark", score: "3:47",        duration: "15 min", pr: true  },
  { id: "h13", date: "Mar 22", wod: "Open gym",          type: "Open gym",  score: "—",           duration: "60 min", pr: false },
  { id: "h14", date: "Mar 21", wod: "Helen",             type: "Benchmark", score: "10:15",       duration: "20 min", pr: false },
  { id: "h15", date: "Mar 19", wod: "DT",                type: "WOD",       score: "7:23",        duration: "25 min", pr: false },
  { id: "h16", date: "Mar 18", wod: "Back Squat 3RM",    type: "Strength",  score: "145 kg",      duration: "45 min", pr: true  },
  { id: "h17", date: "Mar 15", wod: "Amanda",            type: "WOD",       score: "5 rds + 2",   duration: "20 min", pr: false },
  { id: "h18", date: "Mar 14", wod: "Deadlift 5×5",      type: "Strength",  score: "162 kg",      duration: "40 min", pr: false },
  { id: "h19", date: "Mar 12", wod: "Diane",             type: "Benchmark", score: "4:55",        duration: "20 min", pr: false },
  { id: "h20", date: "Mar 11", wod: "Snatch 1RM",        type: "Strength",  score: "78 kg",       duration: "45 min", pr: true  },
]

const ALL_TYPES = ["All", "WOD", "Benchmark", "Strength", "Open gym"] as const
type Filter = (typeof ALL_TYPES)[number]

const typeVariant: Record<string, { variant?: "outline"; color?: "default" | "secondary" }> = {
  WOD:        { color: "default" },
  Benchmark:  { variant: "outline" },
  Strength:   { color: "secondary" },
  "Open gym": { variant: "outline" },
}

export function SessionsHistory() {
  const [filter, setFilter] = useState<Filter>("All")
  const [search, setSearch] = useState("")

  const filtered = history.filter((s) => {
    const matchType = filter === "All" || s.type === filter
    const matchSearch = s.wod.toLowerCase().includes(search.toLowerCase())
    return matchType && matchSearch
  })

  return (
    <Card>
      <CardHeader className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <CardTitle>Session history</CardTitle>
        <div className="flex items-center gap-2 flex-wrap">
          <div className="relative">
            <Search className="absolute left-2.5 top-1/2 -translate-y-1/2 size-3.5 text-muted-foreground" />
            <Input
              placeholder="Search WOD…"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="pl-8 h-8 w-40 text-sm"
            />
          </div>
          <div className="flex gap-1">
            {ALL_TYPES.map((t) => (
              <Button
                key={t}
                size="sm"
                variant={filter === t ? "secondary" : "ghost"}
                className="cursor-pointer px-2.5 text-xs h-7"
                onClick={() => setFilter(t)}
              >
                {t}
              </Button>
            ))}
          </div>
        </div>
      </CardHeader>
      <CardContent className="p-0">
        <div className="divide-y">
          {filtered.length === 0 && (
            <p className="text-sm text-muted-foreground px-6 py-8 text-center">No sessions found.</p>
          )}
          {filtered.map((s) => (
            <div
              key={s.id}
              className="flex items-center gap-4 px-6 py-3 hover:bg-muted/40 transition-colors cursor-pointer group"
            >
              <div className="w-16 shrink-0">
                <p className="text-xs text-muted-foreground">{s.date}</p>
              </div>

              <div className="flex-1 min-w-0 flex items-center gap-2">
                <p className="text-sm font-medium truncate">{s.wod}</p>
                {s.pr && (
                  <Trophy className="size-3.5 text-yellow-500 shrink-0" />
                )}
              </div>

              <Badge {...(typeVariant[s.type] ?? { variant: "outline" })} className="text-xs shrink-0 hidden sm:inline-flex">
                {s.type}
              </Badge>

              <span className={cn(
                "text-sm font-mono font-semibold tabular-nums shrink-0 w-24 text-right",
                s.score === "—" && "text-muted-foreground",
              )}>
                {s.score}
              </span>

              <span className="text-xs text-muted-foreground shrink-0 w-14 text-right hidden md:block">
                {s.duration}
              </span>

              <ChevronRight className="size-4 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity shrink-0" />
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  )
}
