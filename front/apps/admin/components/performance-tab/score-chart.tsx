"use client"

import {
  METRIC,
  type Metric,
  type ScoreEntry,
  resolveScoreValue,
} from "@workspace/api"
import { formatDate } from "@workspace/ui/lib/date"
import { useLocale, useTranslations } from "next-intl"
import {
  CartesianGrid,
  Line,
  LineChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts"

interface ScoreChartProps {
  scores: ScoreEntry[]
  metrics: Metric[]
}

export function ScoreChart({ scores, metrics }: ScoreChartProps) {
  const locale = useLocale()
  const tc = useTranslations("common.performance")

  const formatTime = (v: number) => {
    const m = Math.floor(v / 60)
    const s = v % 60
    return `${m}:${String(s).padStart(2, "0")}`
  }

  const primaryMetric = metrics.includes(METRIC.Time) ? METRIC.Time : metrics[0]
  const isTimePrimary = primaryMetric === METRIC.Time
  const hasSecondaryReps = isTimePrimary && metrics.includes(METRIC.Repetitions)

  const chartData = [...scores]
    .sort(
      (a, b) =>
        new Date(a.performedAt).getTime() -
        new Date(b.performedAt).getTime()
    )
    .map((s) => {
      if (isTimePrimary) {
        return {
          date: formatDate(s.performedAt, locale),
          value: s.time ?? 0,
          metric: s.time != null ? METRIC.Time : null,
          reps: hasSecondaryReps ? s.repetitions : null,
        }
      }

      const resolved = resolveScoreValue(s, metrics)
      return {
        date: formatDate(s.performedAt, locale),
        value: resolved?.value ?? 0,
        metric: resolved?.metric ?? null,
        reps: null,
      }
    })

  const values = chartData.map((d) => d.value)

  const getTimeTicks = (values: number[]) => {
    if (!values.length) return []

    const min = Math.min(...values)
    const max = Math.max(...values)

    const step =
      max > 1800 ? 300 :
        max > 600 ? 120 :
          max > 300 ? 60 :
            30

    const ticks = []

    for (
      let i = Math.floor(min / step) * step;
      i <= max;
      i += step
    ) {
      ticks.push(i)
    }

    return ticks
  }

  return (
    <ResponsiveContainer width="100%" height="100%">
      <LineChart
        data={chartData}
        margin={{ top: 4, right: 8, left: isTimePrimary ? -4 : -16, bottom: 0 }}
      >
        <CartesianGrid
          strokeDasharray="3 3"
          stroke="var(--border)"
        />

        <XAxis
          name="Date"
          dataKey="date"
          tick={{
            fontSize: 11,
            fill: "var(--muted-foreground)",
          }}
          tickLine={false}
          axisLine={false}
        />

        <YAxis
          name="Value"
          dataKey="value"
          tick={{
            fontSize: 11,
            fill: "var(--muted-foreground)",
          }}
          tickLine={false}
          axisLine={false}
          tickFormatter={
            primaryMetric === METRIC.Time
              ? formatTime
              : undefined
          }
          ticks={
            primaryMetric === METRIC.Time
              ? getTimeTicks(values)
              : undefined
          }
          domain={["dataMin", "dataMax"]}
        />

        <Tooltip
          contentStyle={{
            fontSize: 12,
            borderRadius: 8,
            border: "1px solid var(--border)",
            background: "var(--popover)",
            color: "var(--popover-foreground)",
          }}
          labelFormatter={(label) => `📅 ${label}`}
          formatter={(value, _, item) => {
            const { metric, reps } = item.payload

            if (isTimePrimary) {
              if (metric === METRIC.Time) {
                const formatted =
                  reps != null
                    ? `${formatTime(Number(value))} · ${tc("repetitions_count", { value: reps })}`
                    : formatTime(Number(value))

                return [formatted, tc("score")]
              }

              return [
                <>
                  {tc("repetitions_count", { value: reps })}{" "}
                  <small>({tc("not_validated")})</small>
                </>,
                tc("score"),
              ]
            }

            return [value, tc("score")]
          }}
        />

        <Line
          type="monotone"
          name="Value"
          dataKey="value"
          strokeWidth={2}
          stroke="var(--primary)"
          dot={{
            r: 4,
            fill: "var(--primary)",
            stroke: "var(--background)",
            strokeWidth: 2,
          }}
          activeDot={{ r: 5 }}
        />
      </LineChart>
    </ResponsiveContainer>
  )
}