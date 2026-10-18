"use client"

import { useCreateWodScore, type WodDetail } from "@workspace/api"
import { Button } from "@workspace/ui/components/button"
import { Drawer, DrawerContent, DrawerHeader, DrawerTitle, DrawerTrigger } from "@workspace/ui/components/drawer"
import { Input } from "@workspace/ui/components/input"
import { Label } from "@workspace/ui/components/label"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@workspace/ui/components/select"
import { Textarea } from "@workspace/ui/components/textarea"
import { Plus } from "lucide-react"
import { useTranslations } from "next-intl"
import { useState } from "react"

interface WodLogScoreDrawerProps {
  wod: WodDetail
  fullWidth?: boolean
  onSuccess?: () => void
}

function parseScore(raw: string, metric: string): { time?: number; repetitions?: number; weight?: number } {
  if (metric === "time") {
    const parts = raw.trim().split(":")
    if (parts.length === 2) {
      const m = parseInt(parts[0] ?? "0", 10)
      const s = parseInt(parts[1] ?? "0", 10)
      return { time: m * 60 + s }
    }
    return { time: parseInt(raw, 10) }
  }
  if (metric === "weight") {
    return { weight: parseFloat(raw) }
  }
  return { repetitions: parseInt(raw, 10) }
}

function scorePlaceholder(metric: string): string {
  if (metric === "time") return "e.g. 18:42"
  if (metric === "weight") return "e.g. 100.5"
  return "e.g. 22"
}

export function WodLogScoreDrawer({ wod, fullWidth, onSuccess }: WodLogScoreDrawerProps) {
  const tc = useTranslations("common")
  const [open, setOpen] = useState(false)
  const [variantId, setVariantId] = useState<string>(wod.variants[0]?.id ?? "")
  const [date, setDate] = useState(() => new Date().toISOString().slice(0, 10))
  const [score, setScore] = useState("")
  const [note, setNote] = useState("")

  const { mutate, isLoading } = useCreateWodScore()
  const primaryMetric = wod.type.allowedMetrics[0] ?? "repetitions"

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    const scoreFields = parseScore(score, primaryMetric)
    mutate({
      wodId: wod.id,
      wodVersionId: variantId,
      performedAt: new Date(date).toISOString(),
      notes: note || undefined,
      private: false,
      ...scoreFields,
    }).then(() => {
      setOpen(false)
      setScore("")
      setNote("")
      setDate(new Date().toISOString().slice(0, 10))
      onSuccess?.()
    }).catch(() => {
      // error is exposed via `useCreateWodScore().error` if needed
    })
  }

  return (
    <Drawer open={open} onOpenChange={setOpen}>
      <DrawerTrigger asChild>
        <Button
          size="sm"
          className={`cursor-pointer gap-1.5 ${fullWidth ? "w-full" : "h-7 px-2 text-xs"}`}
        >
          <Plus className="size-3.5" />
          {tc("wods.log_score")}
        </Button>
      </DrawerTrigger>
      <DrawerContent>
        <DrawerHeader>
          <DrawerTitle>{tc("wods.log_score")} — {wod.name}</DrawerTitle>
        </DrawerHeader>
        <form onSubmit={handleSubmit} className="flex flex-col gap-4 px-4 pb-8 max-w-md mx-auto w-full">
          {wod.variants.length > 1 && (
            <div className="flex flex-col gap-1.5">
              <Label htmlFor="wod-variant">{tc("wods.variant")}</Label>
              <Select value={variantId} onValueChange={setVariantId}>
                <SelectTrigger id="wod-variant">
                  <SelectValue placeholder={tc("wods.select_variant")} />
                </SelectTrigger>
                <SelectContent>
                  {wod.variants.map(v => (
                    <SelectItem key={v.id} value={v.id}>
                      {v.division.title} · {v.gender}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          )}

          <div className="flex flex-col gap-1.5">
            <Label htmlFor="wod-date">{tc("wods.date")}</Label>
            <Input
              id="wod-date"
              type="date"
              value={date}
              onChange={(e) => setDate(e.target.value)}
              required
            />
          </div>

          <div className="flex flex-col gap-1.5">
            <Label htmlFor="wod-score">{tc("wods.score")}</Label>
            <Input
              id="wod-score"
              placeholder={scorePlaceholder(primaryMetric)}
              value={score}
              onChange={(e) => setScore(e.target.value)}
              required
            />
          </div>

          <div className="flex flex-col gap-1.5">
            <Label htmlFor="wod-note">
              {tc("wods.note")} <span className="text-muted-foreground font-normal">({tc("wods.optional")})</span>
            </Label>
            <Textarea
              id="wod-note"
              placeholder={tc("wods.placeholder_note")}
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
