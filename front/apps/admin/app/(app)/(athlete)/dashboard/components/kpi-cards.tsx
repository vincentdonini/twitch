"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { Progress } from "@workspace/ui/components/progress"
import { Checkbox } from "@workspace/ui/components/checkbox"
import { Badge } from "@workspace/ui/components/badge"
import { Dumbbell, Flame, Zap } from "lucide-react"

const CALORIE_GOAL = 3000
const caloriesCurrent = 2140
const caloriePct = Math.round((caloriesCurrent / CALORIE_GOAL) * 100)

const initialChallenges = [
  { id: "c1", label: "Complete 3 WODs",   done: true  },
  { id: "c2", label: "Log a new PR",       done: true  },
  { id: "c3", label: "Take a rest day",    done: false },
  { id: "c4", label: "10 min mobility",    done: false },
]

export function KpiCards() {
  const [challenges, setChallenges] = useState(initialChallenges)

  const toggleChallenge = (id: string) => {
    setChallenges((prev) =>
      prev.map((c) => (c.id === id ? { ...c, done: !c.done } : c))
    )
  }

  const doneCount = challenges.filter((c) => c.done).length

  return (
    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 *:data-[slot=card]:bg-gradient-to-t *:data-[slot=card]:from-red/5 *:data-[slot=card]:shadow-xs *:data-[slot=card]:to-card">

      {/* WODs complétés */}
      <Card className="bg-blue-500/20">
        <CardContent className="flex items-start gap-4">
          <div className="rounded-lg p-2 shrink-0 bg-blue-500/10 text-blue-500">
            <Dumbbell className="size-5" />
          </div>
          <div className="flex-1 min-w-0">
            <p className="text-sm text-muted-foreground">WODs completed</p>
            <p className="text-2xl font-bold leading-tight">12</p>
            <p className="text-xs text-muted-foreground mt-0.5">This month</p>
            <Badge variant="solid" className="mt-2 text-xs">+2 vs last month</Badge>
          </div>
        </CardContent>
      </Card>

      {/* Current streak */}
      <Card className="bg-orange-500/20">
        <CardContent className="flex items-start gap-4">
          <div className="rounded-lg p-2 shrink-0 bg-orange-500/10 text-orange-500">
            <Flame className="size-5" />
          </div>
          <div className="flex-1 min-w-0">
            <p className="text-sm text-muted-foreground">Current streak</p>
            <p className="text-2xl font-bold leading-tight">5 days</p>
            <p className="text-xs text-muted-foreground mt-0.5">Best: 14 days</p>
            <Badge variant="solid" className="mt-2 text-xs">Keep it up!</Badge>
          </div>
        </CardContent>
      </Card>

      {/* Calories with gauge */}
      <Card className="bg-red-300/20">
        <CardContent className="flex flex-col items-start gap-4">
          <div className="flex items-start gap-4 w-full">
            <div className="rounded-lg p-2 shrink-0 bg-red-500/10 text-red-500">
              <Zap className="size-5" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm text-muted-foreground">Calories burned</p>
              <p className="text-2xl font-bold leading-tight">
                {caloriesCurrent.toLocaleString()}
                <span className="text-sm font-normal text-muted-foreground ml-1">kcal</span>
              </p>
              <p className="text-xs text-muted-foreground mt-0.5">
                Goal: {CALORIE_GOAL.toLocaleString()} kcal / week
              </p>
              <div className="space-y-1 mt-2">
                <Progress value={caloriePct} className="h-2" />
                <p className="text-xs text-muted-foreground text-right">
                  {caloriePct}% — {(CALORIE_GOAL - caloriesCurrent).toLocaleString()} kcal left
                </p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Weekly challenges checklist */}
      {/*<Card>*/}
      {/*  <CardHeader className="pb-2 pt-4 px-4">*/}
      {/*    <CardTitle className="text-sm font-medium text-muted-foreground flex items-center justify-between">*/}
      {/*      Weekly challenges*/}
      {/*      <Badge variant={doneCount === challenges.length ? "default" : "secondary"} className="text-xs">*/}
      {/*        {doneCount}/{challenges.length}*/}
      {/*      </Badge>*/}
      {/*    </CardTitle>*/}
      {/*  </CardHeader>*/}
      {/*  <CardContent className="px-4 pb-4 space-y-2.5">*/}
      {/*    {challenges.map((c) => (*/}
      {/*      <div*/}
      {/*        key={c.id}*/}
      {/*        className="flex items-center gap-2.5 cursor-pointer"*/}
      {/*        onClick={() => toggleChallenge(c.id)}*/}
      {/*      >*/}
      {/*        <Checkbox*/}
      {/*          id={c.id}*/}
      {/*          checked={c.done}*/}
      {/*          onCheckedChange={() => toggleChallenge(c.id)}*/}
      {/*          className="cursor-pointer"*/}
      {/*        />*/}
      {/*        <label*/}
      {/*          htmlFor={c.id}*/}
      {/*          className={`text-sm cursor-pointer select-none ${*/}
      {/*            c.done ? "line-through text-muted-foreground" : ""*/}
      {/*          }`}*/}
      {/*        >*/}
      {/*          {c.label}*/}
      {/*        </label>*/}
      {/*      </div>*/}
      {/*    ))}*/}
      {/*  </CardContent>*/}
      {/*</Card>*/}

    </div>
  )
}
