"use client"

import { useTranslations } from "next-intl"
import { useState } from "react"
import { type Benchmark, METRIC, Metric, useCreateBenchmarkScore } from "@workspace/api"
import { Button } from "@workspace/ui/components/button"
import { Drawer, DrawerContent, DrawerHeader, DrawerTitle, DrawerTrigger } from "@workspace/ui/components/drawer"
import { Input } from "@workspace/ui/components/input"
import { Label } from "@workspace/ui/components/label"
import { Textarea } from "@workspace/ui/components/textarea"
import { Plus } from "lucide-react"

interface BenchmarkLogScoreDrawerProps {
  benchmark: Benchmark
  fullWidth?: boolean
  onSuccess?: () => void
}

function parseScore(raw: string, metric: Metric): { time?: number; repetitions?: number; weight?: number } {
  if (metric === METRIC.Time) {
    const parts = raw.trim().split(":")
    if (parts.length === 2) {
      const m = parseInt(parts[0] ?? "0", 10)
      const s = parseInt(parts[1] ?? "0", 10)
      return { time: m * 60 + s }
    }
    return { time: parseInt(raw, 10) }
  }
  if (metric === METRIC.Weight) return { weight: parseFloat(raw) }
  return { repetitions: parseInt(raw, 10) }
}

function scorePlaceholder(metric: Metric): string {
  if (metric === METRIC.Time) return "e.g. 3:24"
  if (metric === METRIC.Weight) return "e.g. 145"
  return "e.g. 22"
}

export function BenchmarkLogScoreDrawer({ benchmark, fullWidth, onSuccess }: BenchmarkLogScoreDrawerProps) {
  const tc = useTranslations("common")
  const [open, setOpen] = useState(false)
  const [date, setDate] = useState(() => new Date().toISOString().slice(0, 10))
  const [score, setScore] = useState("")
  const [note, setNote] = useState("")

  const { mutate, isLoading } = useCreateBenchmarkScore()

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    const scoreFields = parseScore(score, benchmark.type)
    mutate({
      benchmarkId: benchmark.id,
      performed_at: date,
      notes: note || undefined,
      private: false,
      ...scoreFields,
    }).then(() => {
      setOpen(false)
      setScore("")
      setNote("")
      setDate(new Date().toISOString().slice(0, 10))
      onSuccess?.()
    }).catch(() => {})
  }

  return (
    <Drawer open={open} onOpenChange={setOpen}>
      <DrawerTrigger asChild>
        <Button
          size="sm"
          className={`cursor-pointer gap-1.5 ${fullWidth ? "w-full" : "h-7 px-2 text-xs"}`}
        >
          <Plus className="size-3.5" />
          {tc("benchmarks.log_score")}
        </Button>
      </DrawerTrigger>
      <DrawerContent>
        <DrawerHeader>
          <DrawerTitle>{tc("benchmarks.log_score")} — {benchmark.title || benchmark.name}</DrawerTitle>
        </DrawerHeader>
        <form onSubmit={handleSubmit} className="flex flex-col gap-4 px-4 pb-8 max-w-md mx-auto w-full">
          <div className="flex flex-col gap-1.5">
            <Label htmlFor="bench-date">{tc("benchmarks.date")}</Label>
            <Input
              id="bench-date"
              type="date"
              value={date}
              onChange={(e) => setDate(e.target.value)}
              required
            />
          </div>

          <div className="flex flex-col gap-1.5">
            <Label htmlFor="bench-score">{tc("benchmarks.score")}</Label>
            <Input
              id="bench-score"
              placeholder={scorePlaceholder(benchmark.type)}
              value={score}
              onChange={(e) => setScore(e.target.value)}
              required
            />
          </div>

          <div className="flex flex-col gap-1.5">
            <Label htmlFor="bench-note">
              {tc("benchmarks.note")} <span className="text-muted-foreground font-normal">({tc("benchmarks.optional")})</span>
            </Label>
            <Textarea
              id="bench-note"
              placeholder={tc("benchmarks.placeholder_note")}
              value={note}
              onChange={(e) => setNote(e.target.value)}
              rows={3}
            />
          </div>

          <Button type="submit" disabled={isLoading} className="cursor-pointer w-full mt-2">
            {isLoading ? tc("button.saving") : tc("button.save")}
          </Button>
        </form>
      </DrawerContent>
    </Drawer>
  )
}
