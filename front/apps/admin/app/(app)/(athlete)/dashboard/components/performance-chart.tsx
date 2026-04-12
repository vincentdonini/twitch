"use client"

import { useState } from "react"
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from "recharts"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@workspace/ui/components/card"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@workspace/ui/components/select"

const mockData: Record<string, { week: string; value: number }[]> = {
  back_squat: [
    { week: "W1", value: 100 },
    { week: "W2", value: 105 },
    { week: "W3", value: 102 },
    { week: "W4", value: 110 },
    { week: "W5", value: 112 },
    { week: "W6", value: 115 },
    { week: "W7", value: 118 },
    { week: "W8", value: 122 },
  ],
  deadlift: [
    { week: "W1", value: 140 },
    { week: "W2", value: 145 },
    { week: "W3", value: 145 },
    { week: "W4", value: 150 },
    { week: "W5", value: 152 },
    { week: "W6", value: 155 },
    { week: "W7", value: 160 },
    { week: "W8", value: 162 },
  ],
  clean_jerk: [
    { week: "W1", value: 75 },
    { week: "W2", value: 77 },
    { week: "W3", value: 76 },
    { week: "W4", value: 80 },
    { week: "W5", value: 80 },
    { week: "W6", value: 82 },
    { week: "W7", value: 85 },
    { week: "W8", value: 87 },
  ],
  snatch: [
    { week: "W1", value: 58 },
    { week: "W2", value: 60 },
    { week: "W3", value: 60 },
    { week: "W4", value: 62 },
    { week: "W5", value: 63 },
    { week: "W6", value: 65 },
    { week: "W7", value: 65 },
    { week: "W8", value: 68 },
  ],
}

const liftLabels: Record<string, string> = {
  back_squat: "Back Squat",
  deadlift: "Deadlift",
  clean_jerk: "Clean & Jerk",
  snatch: "Snatch",
}

export function PerformanceChart() {
  const [lift, setLift] = useState("back_squat")
  const data = mockData[lift]
  const first = data[0].value
  const last = data[data.length - 1].value
  const diff = last - first
  const diffLabel = diff > 0 ? `+${diff} kg over 8 weeks` : `${diff} kg over 8 weeks`

  return (
    <Card className="flex-1">
      <CardHeader className="flex flex-row items-start justify-between gap-4">
        <div>
          <CardTitle>Progression — {liftLabels[lift]}</CardTitle>
          <CardDescription>{diffLabel}</CardDescription>
        </div>
        <Select value={lift} onValueChange={setLift}>
          <SelectTrigger className="w-40 cursor-pointer">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            {Object.entries(liftLabels).map(([key, label]) => (
              <SelectItem key={key} value={key}>{label}</SelectItem>
            ))}
          </SelectContent>
        </Select>
      </CardHeader>
      <CardContent>
        <ResponsiveContainer width="100%" height={220}>
          <LineChart data={data} margin={{ top: 4, right: 8, left: -16, bottom: 0 }}>
            <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" />
            <XAxis
              dataKey="week"
              tick={{ fontSize: 12, fill: "var(--muted-foreground)" }}
              axisLine={false}
              tickLine={false}
            />
            <YAxis
              tick={{ fontSize: 12, fill: "var(--muted-foreground)" }}
              axisLine={false}
              tickLine={false}
              unit=" kg"
            />
            <Tooltip
              contentStyle={{
                background: "var(--popover)",
                border: "1px solid var(--border)",
                borderRadius: "8px",
                fontSize: "13px",
              }}
              formatter={(v) => [`${v} kg`, liftLabels[lift]]}
            />
            <Line
              type="monotone"
              dataKey="value"
              stroke="var(--primary)"
              strokeWidth={2.5}
              dot={{ r: 4, fill: "var(--primary)" }}
              activeDot={{ r: 6 }}
            />
          </LineChart>
        </ResponsiveContainer>
      </CardContent>
    </Card>
  )
}
