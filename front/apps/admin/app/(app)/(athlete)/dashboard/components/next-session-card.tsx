import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Badge } from "@workspace/ui/components/badge"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Button } from "@workspace/ui/components/button"
import { CalendarClock, MapPin } from "lucide-react"

const nextSession = {
  date: "Tomorrow",
  time: "07:00",
  wod: "Fran",
  description: "21-15-9 Thrusters (43/30 kg) + Pull-ups",
  coach: { name: "Marie Martin", initials: "MM" },
  location: "Box Alpha — Room A",
  spotsLeft: 4,
}

export function NextSessionCard() {
  return (
    <Card className="flex flex-col">
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          <CalendarClock className="size-4 text-muted-foreground" />
          Next Session
        </CardTitle>
      </CardHeader>
      <CardContent className="flex flex-col gap-4 flex-1">
        <div className="flex items-baseline gap-2">
          <span className="text-2xl font-bold">{nextSession.time}</span>
          <span className="text-muted-foreground text-sm">{nextSession.date}</span>
        </div>

        <div>
          <p className="font-semibold text-lg leading-tight">{nextSession.wod}</p>
          <p className="text-sm text-muted-foreground mt-0.5">{nextSession.description}</p>
        </div>

        <div className="flex items-center gap-2 text-sm text-muted-foreground">
          <MapPin className="size-3.5 shrink-0" />
          {nextSession.location}
        </div>

        <div className="flex items-center gap-2 mt-auto">
          <Avatar className="size-7">
            <AvatarFallback className="text-xs">{nextSession.coach.initials}</AvatarFallback>
          </Avatar>
          <div className="flex-1 min-w-0">
            <p className="text-sm font-medium truncate">{nextSession.coach.name}</p>
            <p className="text-xs text-muted-foreground">Coach</p>
          </div>
          <Badge color="secondary">{nextSession.spotsLeft} spots left</Badge>
        </div>

        <Button className="w-full cursor-pointer mt-2">View details</Button>
      </CardContent>
    </Card>
  )
}
