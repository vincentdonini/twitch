"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { Input } from "@workspace/ui/components/input"
import { CalendarDays, Pencil } from "lucide-react"

const wod = {
  name: "Fran",
  type: "For Time",
  description: "21-15-9\nThrusters (43 / 30 kg)\nPull-ups",
  timeCapMinutes: 10,
  date: "Today — Mar 28",
}

export function WodOfDayCard() {
  const [logging, setLogging] = useState(false)
  const [score, setScore] = useState("")

  return (
    <Card className="flex flex-col">
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
    </Card>
  )
}
