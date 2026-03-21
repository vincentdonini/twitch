"use client"

import type { CreateWodScorePayload, WodDetail } from "@workspace/api"
import { Gender, useCreateWodScore } from "@workspace/api"
import { Button } from "@workspace/ui/components/button"
import { Calendar } from "@workspace/ui/components/calendar"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@workspace/ui/components/dialog"
import { Input } from "@workspace/ui/components/input"
import { Label } from "@workspace/ui/components/label"
import { Popover, PopoverContent, PopoverTrigger } from "@workspace/ui/components/popover"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@workspace/ui/components/select"
import { Switch } from "@workspace/ui/components/switch"
import { Textarea } from "@workspace/ui/components/textarea"
import { CalendarIcon, CheckCircle2 } from "lucide-react"
import { useTranslations } from "next-intl"
import { useEffect, useState } from "react"
import { format } from "date-fns"

interface WodScoreDialogProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  wod: WodDetail
  onSuccess?: () => void
}

export function WodScoreDialog({ open, onOpenChange, wod, onSuccess }: WodScoreDialogProps) {
  const t = useTranslations("wods")
  const { mutate, isLoading } = useCreateWodScore()

  const today = new Date()

  const [variantId, setVariantId] = useState<string>(wod.variants[0]?.id ?? "")
  const [performedAt, setPerformedAt] = useState<Date>(today)
  const [calendarOpen, setCalendarOpen] = useState(false)
  const [scoreMode, setScoreMode] = useState<"finished" | "not_finished">("finished")
  const [minutes, setMinutes] = useState("")
  const [seconds, setSeconds] = useState("")
  const [repetitions, setRepetitions] = useState("")
  const [weight, setWeight] = useState("")
  const [notes, setNotes] = useState("")
  const [isPrivate, setIsPrivate] = useState(false)
  const [success, setSuccess] = useState(false)
  const [timeError, setTimeError] = useState(false)
  const [repsError, setRepsError] = useState(false)

  useEffect(() => {
    if (!open) return
    setVariantId(wod.variants[0]?.id ?? "")
    setPerformedAt(new Date())
    setCalendarOpen(false)
    setScoreMode("finished")
    setMinutes("")
    setSeconds("")
    setRepetitions("")
    setWeight("")
    setNotes("")
    setIsPrivate(false)
    setSuccess(false)
    setTimeError(false)
    setRepsError(false)
  }, [open])

  const allowedMetrics: string[] = wod.type.allowedMetrics ?? []
  const selectedVariant = wod.variants.find(v => v.id === variantId)
  const hasTimeCap = allowedMetrics.includes("time") && (selectedVariant?.timeCap ?? 0) > 0
  const maxReps = hasTimeCap
    ? (selectedVariant?.exercises ?? []).reduce((sum, ex) => {
      const repMetric = ex.metrics.find(m => m.type === "repetitions")
      return sum + (repMetric?.value ?? 0)
    }, 0)
    : null

  function handleVariantChange(id: string) {
    setVariantId(id)
    setScoreMode("finished")
    setMinutes("")
    setSeconds("")
    setRepetitions("")
  }

  function handleScoreModeChange(mode: "finished" | "not_finished") {
    setScoreMode(mode)
    setMinutes("")
    setSeconds("")
    setRepetitions("")
  }

  const genderLabel: Record<Gender, string> = {
    [Gender.MALE]: t("gender_male"),
    [Gender.FEMALE]: t("gender_female"),
    [Gender.MIXED]: t("gender_mixed"),
  }

  function variantLabel(v: WodDetail["variants"][number]): string {
    const parts = [v.division.title, genderLabel[v.gender]]
    if (v.ageRange) parts.push(v.ageRange.title)
    return parts.join(" · ")
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault()
    setTimeError(false)
    setRepsError(false)

    const timeCap = selectedVariant?.timeCap ?? null
    if (timeCap !== null && scoreMode === "finished") {
      const totalSeconds = parseInt(minutes || "0") * 60 + parseInt(seconds || "0")
      if (totalSeconds > timeCap) {
        setTimeError(true)
        return
      }
    }

    if (maxReps !== null && scoreMode === "not_finished") {
      if (parseInt(repetitions) >= maxReps) {
        setRepsError(true)
        return
      }
    }

    const payload: CreateWodScorePayload = {
      wodId: wod.id,
      wodVersionId: variantId,
      performedAt: format(performedAt, "yyyy-MM-dd"),
      private: isPrivate,
      ...(allowedMetrics.includes("time") && !hasTimeCap && {
        time: parseInt(minutes || "0") * 60 + parseInt(seconds || "0"),
      }),
      ...(hasTimeCap && scoreMode === "finished" && {
        time: parseInt(minutes || "0") * 60 + parseInt(seconds || "0"),
      }),
      ...(hasTimeCap && scoreMode === "not_finished" && {
        repetitions: parseInt(repetitions),
      }),
      ...(allowedMetrics.includes("repetitions") && !hasTimeCap && {
        repetitions: parseInt(repetitions),
      }),
      ...(allowedMetrics.includes("weight") && {
        weight: parseFloat(weight),
      }),
      ...(notes && { notes }),
    }

    await mutate(payload)
    setSuccess(true)
    onSuccess?.()
    setTimeout(() => {
      setSuccess(false)
      onOpenChange(false)
    }, 1500)
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-md">
        {success ? (
          <div className="flex flex-col items-center gap-2 py-8 text-center">
            <CheckCircle2 className="size-10 text-green-500" />
            <p className="font-medium">{t("score_success")}</p>
          </div>
        ) : (
          <>
            <DialogHeader>
              <DialogTitle>
                {t("log_score")}
              </DialogTitle>
              <DialogDescription>
                {t("log_score_description", {
                  wodName: wod.name,
                })}
              </DialogDescription>
            </DialogHeader>

            <form onSubmit={handleSubmit}>
              <div className="-mx-4 no-scrollbar max-h-[50vh] overflow-y-auto px-4 space-y-4 py-5">
                {/* Variant selector */}
                {wod.variants.length > 1 && (
                  <div className="space-y-1.5">
                    <Label>{t("score_variant")}</Label>
                    <Select value={variantId} onValueChange={handleVariantChange}>
                      <SelectTrigger className="w-full">
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        {wod.variants.map(v => (
                          <SelectItem key={v.id} value={v.id}>
                            {variantLabel(v)}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                )}

                {/* Toggle finished / not finished (time cap only) */}
                {hasTimeCap && (
                  <div className="grid grid-cols-2 gap-1 rounded-lg bg-muted p-1">
                    <button
                      type="button"
                      onClick={() => handleScoreModeChange("finished")}
                      className={`rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${
                        scoreMode === "finished"
                          ? "bg-background text-foreground shadow-sm"
                          : "text-muted-foreground hover:text-foreground"
                      }`}
                    >
                      {t("score_finished")}
                    </button>
                    <button
                      type="button"
                      onClick={() => handleScoreModeChange("not_finished")}
                      className={`rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${
                        scoreMode === "not_finished"
                          ? "bg-background text-foreground shadow-sm"
                          : "text-muted-foreground hover:text-foreground"
                      }`}
                    >
                      {t("score_not_finished")}
                    </button>
                  </div>
                )}

                {/* Time — affiché si allowedMetrics time sans time cap, ou time cap + finished */}
                {((allowedMetrics.includes("time") && !hasTimeCap) || (hasTimeCap && scoreMode === "finished")) && (
                  <div className="space-y-1.5">
                    <Label>{t("score_time")}</Label>
                    <div className="flex items-center gap-2">
                      <div className="relative flex-1">
                        <Input
                          type="number"
                          min={0}
                          placeholder="0"
                          value={minutes}
                          onChange={e => setMinutes(e.target.value)}
                          className="pr-12"
                        />
                        <span
                          className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground pointer-events-none">
                      {t("score_time_min")}
                    </span>
                      </div>
                      <div className="relative flex-1">
                        <Input
                          type="number"
                          min={0}
                          max={59}
                          placeholder="0"
                          value={seconds}
                          onChange={e => setSeconds(e.target.value)}
                          className="pr-12"
                        />
                        <span
                          className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground pointer-events-none">
                      {t("score_time_sec")}
                    </span>
                      </div>
                    </div>
                    {timeError && (
                      <p className="text-xs text-destructive">
                        {t("score_time_exceeds_cap", {
                          cap: `${Math.floor((selectedVariant!.timeCap!) / 60)}:${String((selectedVariant!.timeCap!) % 60).padStart(2, "0")}`,
                        })}
                      </p>
                    )}
                  </div>
                )}

                {/* Reps — affiché si allowedMetrics reps sans time cap, ou time cap + not_finished */}
                {((allowedMetrics.includes("repetitions") && !hasTimeCap) || (hasTimeCap && scoreMode === "not_finished")) && (
                  <div className="space-y-1.5">
                    <Label>{t("score_repetitions")}</Label>
                    <Input
                      type="number"
                      min={0}
                      placeholder="0"
                      value={repetitions}
                      onChange={e => setRepetitions(e.target.value)}
                      required
                    />
                    {repsError && maxReps !== null && (
                      <p className="text-xs text-destructive">
                        {t("score_reps_exceeds_max", { max: maxReps })}
                      </p>
                    )}
                  </div>
                )}

                {/* Weight */}
                {allowedMetrics.includes("weight") && (
                  <div className="space-y-1.5">
                    <Label>{t("score_weight")}</Label>
                    <div className="relative">
                      <Input
                        type="number"
                        min={0}
                        step={0.5}
                        placeholder="0"
                        value={weight}
                        onChange={e => setWeight(e.target.value)}
                        className="pr-10"
                        required
                      />
                      <span
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-muted-foreground pointer-events-none">
                    kg
                  </span>
                    </div>
                  </div>
                )}

                {/* Date */}
                <div className="space-y-1.5">
                  <Label>{t("score_performed_at")}</Label>
                  <Popover open={calendarOpen} onOpenChange={setCalendarOpen}>
                    <PopoverTrigger asChild>
                      <Button
                        type="button"
                        variant="outline"
                        className="w-full justify-start text-left font-normal"
                      >
                        <CalendarIcon className="mr-2 size-4" />
                        {format(performedAt, "PPP")}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="single"
                        selected={performedAt}
                        onSelect={(date) => {
                          if (date) {
                            setPerformedAt(date)
                            setCalendarOpen(false)
                          }
                        }}
                        disabled={(date) => date > today}
                        autoFocus
                      />
                    </PopoverContent>
                  </Popover>
                </div>

                {/* Notes */}
                <div className="space-y-1.5">
                  <Label className="flex items-center gap-1.5">
                    {t("score_notes")}
                    <span className="text-xs font-normal text-muted-foreground">({t("score_optional")})</span>
                  </Label>
                  <Textarea
                    placeholder={t("score_notes_placeholder")}
                    value={notes}
                    onChange={e => setNotes(e.target.value)}
                    rows={2}
                    className="resize-none"
                  />
                </div>

                {/* Private */}
                <div className="flex items-center justify-between rounded-lg border px-3 py-2.5">
                  <div className="space-y-0.5">
                    <p className="text-sm font-medium">{t("score_private")}</p>
                    <p className="text-xs text-muted-foreground">{t("score_private_hint")}</p>
                  </div>
                  <Switch checked={isPrivate} onCheckedChange={setIsPrivate} />
                </div>
              </div>
              <DialogFooter>
                <Button type="submit" className="w-full" disabled={isLoading}>
                  {isLoading ? t("score_submitting") : t("score_submit")}
                </Button>
              </DialogFooter>
            </form>
          </>
        )}
      </DialogContent>
    </Dialog>
  )
}
