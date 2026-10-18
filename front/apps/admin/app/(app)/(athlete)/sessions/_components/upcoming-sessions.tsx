import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { CalendarClock, MapPin } from "lucide-react"

type Session = {
  id: string
  date: string
  dayLabel: string
  time: string
  type: "WOD" | "Strength" | "Open gym"
  gym: string
  wod?: string
}

const upcoming: Session[] = [
  { id: "u1", date: "Today",     dayLabel: "Sat 12 Apr", time: "18:30", type: "WOD",      gym: "CrossFit Levallois", wod: "Fran" },
  { id: "u2", date: "Tomorrow",  dayLabel: "Sun 13 Apr", time: "07:00", type: "Strength", gym: "CrossFit Levallois" },
  { id: "u3", date: "Tuesday",   dayLabel: "Tue 15 Apr", time: "18:30", type: "WOD",      gym: "CrossFit Levallois" },
  { id: "u4", date: "Wednesday", dayLabel: "Wed 16 Apr", time: "07:00", type: "Open gym", gym: "CrossFit Levallois" },
  { id: "u5", date: "Thursday",  dayLabel: "Thu 17 Apr", time: "18:30", type: "WOD",      gym: "CrossFit Levallois" },
  { id: "u6", date: "Saturday",  dayLabel: "Sat 19 Apr", time: "09:00", type: "WOD",      gym: "CrossFit Levallois", wod: "Murph" },
]

const typeVariant: Record<Session["type"], { variant?: "outline"; color?: "default" | "secondary" }> = {
  WOD:        { color: "default" },
  Strength:   { color: "secondary" },
  "Open gym": { variant: "outline" },
}

export function UpcomingSessions() {
  return (
    <Card className="flex flex-col">
      <CardHeader className="flex flex-row items-center justify-between">
        <CardTitle className="flex items-center gap-2">
          <CalendarClock className="size-4 text-muted-foreground" />
          Upcoming sessions
        </CardTitle>
      </CardHeader>
      <CardContent className="flex flex-col gap-2 flex-1">
        {upcoming.map((s) => (
          <div
            key={s.id}
            className="flex items-center gap-3 rounded-lg border px-3 py-2.5 hover:bg-muted/30 transition-colors"
          >
            <div className="shrink-0 text-center w-12">
              <p className="text-xs text-muted-foreground leading-tight">{s.date.slice(0, 3)}</p>
              <p className="text-sm font-semibold leading-tight tabular-nums">{s.time}</p>
            </div>
            <div className="w-px self-stretch bg-border shrink-0" />
            <div className="flex-1 min-w-0">
              <div className="flex items-center gap-1.5">
                <Badge {...typeVariant[s.type]} className="text-xs shrink-0">
                  {s.type}
                </Badge>
                {s.wod && (
                  <span className="text-sm font-medium truncate">{s.wod}</span>
                )}
              </div>
              <p className="text-xs text-muted-foreground mt-0.5 flex items-center gap-1 truncate">
                <MapPin className="size-3 shrink-0" />
                {s.gym}
              </p>
            </div>
            <Button variant="ghost" size="sm" className="cursor-pointer shrink-0 text-xs h-7 px-2">
              {s.id === "u1" ? "Check-in" : "Book"}
            </Button>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}
