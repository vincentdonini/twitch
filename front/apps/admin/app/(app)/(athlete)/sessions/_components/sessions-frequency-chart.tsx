"use client"

import { useState } from "react"
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
} from "recharts"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import { Button } from "@workspace/ui/components/button"

const data12w = [
  { week: "Jan 20", wod: 2, strength: 1, openGym: 0 },
  { week: "Jan 27", wod: 2, strength: 1, openGym: 1 },
  { week: "Feb 3",  wod: 1, strength: 1, openGym: 0 },
  { week: "Feb 10", wod: 3, strength: 1, openGym: 0 },
  { week: "Feb 17", wod: 2, strength: 1, openGym: 0 },
  { week: "Feb 24", wod: 2, strength: 2, openGym: 0 },
  { week: "Mar 3",  wod: 2, strength: 1, openGym: 0 },
  { week: "Mar 10", wod: 2, strength: 1, openGym: 0 },
  { week: "Mar 17", wod: 2, strength: 2, openGym: 0 },
  { week: "Mar 24", wod: 2, strength: 1, openGym: 0 },
  { week: "Mar 31", wod: 2, strength: 1, openGym: 0 },
  { week: "Apr 7",  wod: 3, strength: 1, openGym: 0 },
]

const data6w = data12w.slice(-6)
const data4w = data12w.slice(-4)

const ranges = [
  { label: "4w", data: data4w },
  { label: "6w", data: data6w },
  { label: "3m", data: data12w },
] as const

type Range = (typeof ranges)[number]["label"]

export function SessionsFrequencyChart() {
  const [range, setRange] = useState<Range>("3m")
  const selected = ranges.find((r) => r.label === range)!
  const total = selected.data.reduce((s, d) => s + d.wod + d.strength + d.openGym, 0)

  return (
    <Card className="flex flex-col">
      <CardHeader className="flex flex-row items-start justify-between gap-4">
        <div>
          <CardTitle>Session frequency</CardTitle>
          <CardDescription>{total} sessions over the selected period</CardDescription>
        </div>
        <div className="flex gap-1 shrink-0">
          {ranges.map((r) => (
            <Button
              key={r.label}
              size="sm"
              variant={range === r.label ? "secondary" : "ghost"}
              className="cursor-pointer px-2.5 text-xs h-7"
              onClick={() => setRange(r.label)}
            >
              {r.label}
            </Button>
          ))}
        </div>
      </CardHeader>
      <CardContent className="flex-1">
        <ResponsiveContainer width="100%" height={220}>
          <BarChart data={selected.data} margin={{ top: 4, right: 4, left: -20, bottom: 0 }} barSize={16}>
            <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" vertical={false} />
            <XAxis
              dataKey="week"
              tick={{ fontSize: 11, fill: "var(--muted-foreground)" }}
              axisLine={false}
              tickLine={false}
            />
            <YAxis
              tick={{ fontSize: 11, fill: "var(--muted-foreground)" }}
              axisLine={false}
              tickLine={false}
              allowDecimals={false}
            />
            <Tooltip
              cursor={{ fill: "var(--muted)", opacity: 0.4 }}
              contentStyle={{
                background: "var(--popover)",
                border: "1px solid var(--border)",
                borderRadius: "8px",
                fontSize: "12px",
              }}
            />
            <Legend
              iconType="circle"
              iconSize={8}
              wrapperStyle={{ fontSize: "12px", paddingTop: "12px" }}
            />
            <Bar dataKey="wod" name="WOD" stackId="a" fill="var(--color-primary, hsl(var(--primary)))" radius={[0, 0, 0, 0]} />
            <Bar dataKey="strength" name="Strength" stackId="a" fill="hsl(var(--secondary-foreground)/0.4)" radius={[0, 0, 0, 0]} />
            <Bar dataKey="openGym" name="Open gym" stackId="a" fill="var(--muted-foreground)" radius={[4, 4, 0, 0]} />
          </BarChart>
        </ResponsiveContainer>
      </CardContent>
    </Card>
  )
}
