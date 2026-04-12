import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Button } from "@workspace/ui/components/button"
import { CalendarClock, ArrowRight } from "lucide-react"

const sessions = [
  { date: "Tomorrow", time: "07:00", label: "Thu 29 Mar", type: "WOD" },
  { date: "Friday",   time: "18:30", label: "Fri 30 Mar", type: "Strength" },
  { date: "Saturday", time: "09:00", label: "Sat 31 Mar", type: "WOD" },
  { date: "Monday",   time: "07:00", label: "Mon 2 Apr",  type: "Open gym" },
]

const typeVariant: Record<string, { variant?: "outline"; color?: "default" | "secondary" }> = {
  WOD: { color: "default" },
  Strength: { color: "secondary" },
  "Open gym": { variant: "outline" },
}

export function UpcomingSessionsCard() {
  return (
    <Card className="flex flex-col">
      <CardHeader className="flex flex-row items-center justify-between">
        <CardTitle className="flex items-center gap-2">
          <CalendarClock className="size-4 text-muted-foreground" />
          Upcoming Sessions
        </CardTitle>
        <Button variant="ghost" size="sm" className="cursor-pointer text-muted-foreground gap-1">
          View all <ArrowRight className="size-3.5" />
        </Button>
      </CardHeader>
      <CardContent className="flex flex-col gap-3 flex-1">
        {sessions.map((s) => (
          <div
            key={`${s.label}-${s.time}`}
            className="flex items-center gap-3 rounded-lg border px-3 py-2.5"
          >
            <div className="shrink-0 text-center w-10">
              <p className="text-xs text-muted-foreground">{s.date.slice(0, 3)}</p>
              <p className="text-sm font-semibold leading-tight">{s.time}</p>
            </div>
            <div className="w-px self-stretch bg-border shrink-0" />
            <p className="flex-1 text-sm">{s.label}</p>
            <Badge {...(typeVariant[s.type] ?? { variant: "outline" })} className="text-xs shrink-0">
              {s.type}
            </Badge>
          </div>
        ))}
      </CardContent>
    </Card>
  )
}
