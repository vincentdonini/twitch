"use client"

import { Button } from "@workspace/ui/components/button"
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from "@workspace/ui/components/form"
import { Input } from "@workspace/ui/components/input"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@workspace/ui/components/select"
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle } from "@workspace/ui/components/sheet"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { Badge } from "@workspace/ui/components/badge"
import { zodResolver } from "@hookform/resolvers/zod"
import {
  type Wod,
  type WodVariant,
  MetricType,
  useCreateWod,
  useGetWod,
  useGetWodCategories,
  useGetWodTypes,
  useUpdateWod,
  useCreateWodVariant,
  useUpdateWodVariant,
  useDeleteWodVariant,
  useGetWodDivisions,
  useGetWodAgeRanges,
  useGetExercises,
} from "@workspace/api"
import { useTranslations } from "next-intl"
import { useEffect, useRef, useState } from "react"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { Pencil, Plus, Trash2, X } from "lucide-react"

function buildSchema(t: ReturnType<typeof useTranslations<"admin_wods">>) {
  return z.object({
    name: z.string().min(1, t("val_name_required")),
    typeId: z.string().min(1, t("val_type_required")),
    categoryId: z.string().min(1, t("val_category_required")),
    teamSize: z.number().int().positive().nullable().optional(),
  })
}

type FormValues = z.infer<ReturnType<typeof buildSchema>>

const variantSchema = z.object({
  divisionId: z.string().min(1),
  gender: z.string().nullable().optional(),
  ageRangeId: z.string().nullable().optional(),
  rounds: z.number().int().positive().nullable().optional(),
  timeCap: z.number().int().positive().nullable().optional(),
})

type VariantFormValues = z.infer<typeof variantSchema>

interface WodSheetProps {
  mode: "create" | "edit"
  wod?: Wod
  open: boolean
  onOpenChange: (open: boolean) => void
  onSuccess: () => void
}

export function WodSheet({ mode, wod, open, onOpenChange, onSuccess }: WodSheetProps) {
  const t = useTranslations("admin_wods")

  const { data: types, isLoading: typesLoading } = useGetWodTypes()
  const { data: categories, isLoading: categoriesLoading } = useGetWodCategories()
  const { data: detail, isLoading: detailLoading, refetch } = useGetWod(wod?.id ?? "", open && mode === "edit")

  const { mutate: create, isLoading: creating, error: createError, reset: resetCreate } = useCreateWod()
  const { mutate: update, isLoading: updating, error: updateError, reset: resetUpdate } = useUpdateWod(wod?.id ?? "")

  const isLoading = creating || updating
  const error = createError ?? updateError

  const [activeVariant, setActiveVariant] = useState<string | "new" | null>(null)

  const form = useForm<FormValues>({
    resolver: zodResolver(buildSchema(t)),
    defaultValues: { name: "", typeId: "", categoryId: "", teamSize: null },
  })

  useEffect(() => {
    if (mode === "edit" && wod) {
      form.reset({
        name: wod.name,
        typeId: wod.type.id,
        categoryId: wod.category.id,
        teamSize: wod.teamSize ?? null,
      })
    } else if (mode === "create") {
      form.reset({ name: "", typeId: "", categoryId: "", teamSize: null })
    }
  }, [wod, mode, form])

  useEffect(() => {
    if (!open) {
      resetCreate()
      resetUpdate()
      setActiveVariant(null)
    }
  }, [open, resetCreate, resetUpdate])

  async function onSubmit(values: FormValues) {
    try {
      if (mode === "create") {
        await create({
          name: values.name,
          typeId: values.typeId,
          categoryId: values.categoryId,
          teamSize: values.teamSize ?? null,
        })
      } else {
        await update({
          name: values.name,
          typeId: values.typeId,
          categoryId: values.categoryId,
          teamSize: values.teamSize ?? null,
        })
      }
      onSuccess()
    } catch {
      // error already set by hook
    }
  }

  const variants = detail?.variants ?? wod?.variants ?? []

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent className="w-full max-w-screen sm:max-w-screen md:max-w-screen xl:max-w-[50vw] overflow-y-auto">
        <SheetHeader>
          <SheetTitle>{mode === "create" ? t("create_title") : t("edit_title")}</SheetTitle>
          <SheetDescription>
            {mode === "create" ? t("create_description") : t("edit_description")}
          </SheetDescription>
        </SheetHeader>

        <Tabs defaultValue="info" className="mt-6">
          <TabsList className="w-auto mx-4">
            <TabsTrigger value="info">{t("tab_info")}</TabsTrigger>
            {mode === "edit" && (
              <TabsTrigger value="variants">{t("tab_variants")}</TabsTrigger>
            )}
          </TabsList>

          {/* ── Info tab ── */}
          <TabsContent value="info">
            <Form {...form}>
              <form onSubmit={form.handleSubmit(onSubmit)} className="mt-4 space-y-4 px-4">
                <FormField
                  control={form.control}
                  name="name"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>{t("field_name")}</FormLabel>
                      <FormControl>
                        <Input placeholder={t("field_name_placeholder")} {...field} />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                <div className="grid grid-cols-2 gap-4">
                  <FormField
                    control={form.control}
                    name="typeId"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>{t("field_type")}</FormLabel>
                        <Select
                          onValueChange={field.onChange}
                          value={field.value}
                          disabled={typesLoading}
                        >
                          <FormControl>
                            <SelectTrigger>
                              <SelectValue placeholder={t("field_type_placeholder")} />
                            </SelectTrigger>
                          </FormControl>
                          <SelectContent>
                            {(types ?? []).map((type) => (
                              <SelectItem key={type.id} value={type.id}>
                                {type.title}
                              </SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="categoryId"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>{t("field_category")}</FormLabel>
                        <Select
                          onValueChange={field.onChange}
                          value={field.value}
                          disabled={categoriesLoading}
                        >
                          <FormControl>
                            <SelectTrigger>
                              <SelectValue placeholder={t("field_category_placeholder")} />
                            </SelectTrigger>
                          </FormControl>
                          <SelectContent>
                            {(categories ?? []).map((cat) => (
                              <SelectItem key={cat.id} value={cat.id}>
                                {cat.title}
                              </SelectItem>
                            ))}
                          </SelectContent>
                        </Select>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </div>

                <FormField
                  control={form.control}
                  name="teamSize"
                  render={({ field }) => (
                    <FormItem>
                      <FormLabel>
                        {t("field_team_size")}{" "}
                        <span className="text-muted-foreground text-xs">(optional)</span>
                      </FormLabel>
                      <FormControl>
                        <Input
                          type="number"
                          min="1"
                          step="1"
                          placeholder={t("field_team_size_placeholder")}
                          value={field.value ?? ""}
                          onChange={(e) => {
                            const v = e.target.value
                            field.onChange(v === "" ? null : parseInt(v, 10))
                          }}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  )}
                />

                {error && <p className="text-sm text-destructive">{error}</p>}

                <div className="flex justify-end gap-2 pt-4">
                  <Button
                    type="button"
                    variant="outline"
                    className="cursor-pointer"
                    onClick={() => onOpenChange(false)}
                  >
                    {t("btn_cancel")}
                  </Button>
                  <Button type="submit" disabled={isLoading} className="cursor-pointer">
                    {isLoading
                      ? mode === "create" ? t("btn_creating") : t("btn_saving")
                      : mode === "create" ? t("btn_create") : t("btn_save")
                    }
                  </Button>
                </div>
              </form>
            </Form>
          </TabsContent>

          {/* ── Variants tab ── */}
          {mode === "edit" && (
            <TabsContent value="variants" className="mt-4 px-4">
              <div className="space-y-4">
                {activeVariant === null && (
                  <Button
                    type="button"
                    size="sm"
                    variant="outline"
                    className="cursor-pointer"
                    onClick={() => setActiveVariant("new")}
                  >
                    <Plus className="size-4 mr-1.5" />
                    {t("variant_add")}
                  </Button>
                )}

                {activeVariant === "new" && (
                  <VariantForm
                    mode="create"
                    wodId={wod!.id}
                    onSaved={() => {
                      setActiveVariant(null)
                      refetch()
                    }}
                    onCancel={() => setActiveVariant(null)}
                  />
                )}

                {detailLoading && (
                  <p className="py-8 text-center text-sm text-muted-foreground">{t("variants_loading")}</p>
                )}

                {!detailLoading && variants.length === 0 && activeVariant === null && (
                  <p className="py-4 text-center text-sm text-muted-foreground">{t("variants_empty")}</p>
                )}

                {!detailLoading && variants.length > 0 && (
                  <ul className="space-y-3">
                    {variants.map((variant) =>
                      activeVariant === variant.id ? (
                        <li key={variant.id}>
                          <VariantForm
                            mode="edit"
                            variant={variant}
                            onSaved={() => {
                              setActiveVariant(null)
                              refetch()
                            }}
                            onCancel={() => setActiveVariant(null)}
                          />
                        </li>
                      ) : (
                        <li key={variant.id} className="rounded-lg border p-3 space-y-2">
                          <div className="flex items-center gap-2">
                            <div className="flex items-center gap-1.5 flex-wrap flex-1 min-w-0">
                              <Badge variant="outline">{variant.division.title}</Badge>
                              {variant.gender && (
                                <Badge color="secondary">
                                  {variant.gender === "male"
                                    ? t("variant_gender_male")
                                    : variant.gender === "female"
                                      ? t("variant_gender_female")
                                      : t("variant_gender_mixed")}
                                </Badge>
                              )}
                              {variant.rounds != null && (
                                <span className="text-xs text-muted-foreground">
                                  {t("variant_rounds", { count: variant.rounds })}
                                </span>
                              )}
                              {variant.timeCap != null && (
                                <span className="text-xs text-muted-foreground">
                                  {t("variant_timecap", { minutes: Math.floor(variant.timeCap / 60) })}
                                </span>
                              )}
                            </div>
                            {activeVariant === null && (
                              <div className="flex gap-1 shrink-0">
                                <Button
                                  type="button"
                                  size="icon"
                                  variant="ghost"
                                  className="size-7 cursor-pointer"
                                  onClick={() => setActiveVariant(variant.id)}
                                >
                                  <Pencil className="size-3.5" />
                                </Button>
                                <DeleteVariantButton
                                  variantId={variant.id}
                                  onDeleted={() => refetch()}
                                />
                              </div>
                            )}
                          </div>

                          {variant.exercises.length > 0 && (
                            <ul className="space-y-1 pl-1">
                              {variant.exercises.map((ex) => (
                                <li key={ex.id} className="flex items-start gap-2 text-sm">
                                  <span className="text-muted-foreground w-4 shrink-0">{ex.position}.</span>
                                  <div className="min-w-0">
                                    <span className="font-medium">{ex.exercise.title}</span>
                                    {ex.metrics.length > 0 && (
                                      <span className="ml-2 text-muted-foreground">
                                        {ex.metrics.map((m) => `${m.value} ${m.type}`).join(" / ")}
                                      </span>
                                    )}
                                  </div>
                                </li>
                              ))}
                            </ul>
                          )}
                        </li>
                      ),
                    )}
                  </ul>
                )}
              </div>
            </TabsContent>
          )}
        </Tabs>
      </SheetContent>
    </Sheet>
  )
}

// ─── VariantForm ──────────────────────────────────────────────────────────────

type ExerciseRow = {
  exerciseId: string
  exerciseName: string
  metricType: string
  metricValue: string
}

interface VariantFormProps {
  mode: "create" | "edit"
  variant?: WodVariant
  wodId?: string
  onSaved: () => void
  onCancel: () => void
}

function VariantForm({ mode, variant, wodId, onSaved, onCancel }: VariantFormProps) {
  const t = useTranslations("admin_wods")

  const { data: divisions } = useGetWodDivisions()
  const { data: ageRanges } = useGetWodAgeRanges()

  const { mutate: create, isLoading: creating, error: createError } = useCreateWodVariant(wodId ?? "")
  const { mutate: update, isLoading: updating, error: updateError } = useUpdateWodVariant(variant?.id ?? "")

  const isLoading = creating || updating
  const error = mode === "create" ? createError : updateError

  const [exercises, setExercises] = useState<ExerciseRow[]>(() =>
    variant?.exercises.map((e) => ({
      exerciseId: e.exercise.id,
      exerciseName: e.exercise.title,
      metricType: e.metrics[0]?.type ?? MetricType.REPETITIONS,
      metricValue: e.metrics[0]?.value != null ? String(e.metrics[0].value) : "",
    })) ?? [],
  )

  const [exerciseSearch, setExerciseSearch] = useState("")
  const [showResults, setShowResults] = useState(false)
  const searchRef = useRef<HTMLDivElement>(null)

  const { data: searchResults } = useGetExercises(
    exerciseSearch.length >= 2 ? { "filters[title][like]": exerciseSearch } : {},
  )

  // Close dropdown on outside click
  useEffect(() => {
    function handleClick(e: MouseEvent) {
      if (searchRef.current && !searchRef.current.contains(e.target as Node)) {
        setShowResults(false)
      }
    }

    document.addEventListener("mousedown", handleClick)
    return () => document.removeEventListener("mousedown", handleClick)
  }, [])

  function addExercise(id: string, name: string) {
    setExercises((prev) => [
      ...prev,
      { exerciseId: id, exerciseName: name, metricType: MetricType.REPETITIONS, metricValue: "" },
    ])
    setExerciseSearch("")
    setShowResults(false)
  }

  function removeExercise(index: number) {
    setExercises((prev) => prev.filter((_, i) => i !== index))
  }

  function updateExerciseMetricType(index: number, type: string) {
    setExercises((prev) => prev.map((e, i) => i === index ? { ...e, metricType: type } : e))
  }

  function updateExerciseMetricValue(index: number, value: string) {
    setExercises((prev) => prev.map((e, i) => i === index ? { ...e, metricValue: value } : e))
  }

  // ── Form ────────────────────────────────────────────────────────────────────
  const form = useForm<VariantFormValues>({
    resolver: zodResolver(variantSchema),
    defaultValues: {
      divisionId: variant?.division.id ?? "",
      gender: variant?.gender ?? null,
      ageRangeId: variant?.ageRange?.id ?? null,
      rounds: variant?.rounds ?? null,
      timeCap: variant?.timeCap != null ? Math.floor(variant.timeCap / 60) : null,
    },
  })

  async function onSubmit(values: VariantFormValues) {
    try {
      const payload = {
        divisionId: values.divisionId,
        gender: values.gender ?? null,
        ageRangeId: values.ageRangeId ?? null,
        rounds: values.rounds ?? null,
        timeCap: values.timeCap != null ? values.timeCap * 60 : null,
        exercises: exercises
          .filter((e) => e.exerciseId)
          .map((e) => ({
            exerciseId: e.exerciseId,
            metrics: e.metricValue
              ? [{ type: e.metricType, value: parseFloat(e.metricValue) }]
              : [],
          })),
      }

      if (mode === "create") {
        await create(payload)
      } else {
        await update(payload)
      }
      onSaved()
    } catch {
      // error already set by hook
    }
  }

  const metricOptions = [
    { value: MetricType.REPETITIONS, label: "Reps" },
    { value: MetricType.TIME, label: "Time (s)" },
    { value: MetricType.DISTANCE, label: "Distance (m)" },
    { value: MetricType.CALORIES, label: "Calories" },
  ]

  return (
    <div className="rounded-lg border p-4 space-y-3 bg-muted/30">
      <p className="text-sm font-medium">
        {mode === "create" ? t("variant_add") : t("variant_edit")}
      </p>
      <Form {...form}>
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-3">
          <div className="grid grid-cols-2 gap-3">
            <FormField
              control={form.control}
              name="divisionId"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>{t("variant_field_division")}</FormLabel>
                  <Select onValueChange={field.onChange} value={field.value}>
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder={t("variant_field_division_placeholder")} />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      {(divisions ?? []).map((d) => (
                        <SelectItem key={d.id} value={d.id}>{d.title}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="gender"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>{t("variant_field_gender")}</FormLabel>
                  <Select
                    onValueChange={(v) => field.onChange(v === "__none" ? null : v)}
                    value={field.value ?? "__none"}
                  >
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder={t("variant_field_gender_placeholder")} />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectItem value="__none">{t("variant_field_gender_placeholder")}</SelectItem>
                      <SelectItem value="male">{t("variant_gender_male")}</SelectItem>
                      <SelectItem value="female">{t("variant_gender_female")}</SelectItem>
                      <SelectItem value="mixed">{t("variant_gender_mixed")}</SelectItem>
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              )}
            />
          </div>

          <div className="grid grid-cols-3 gap-3">
            <FormField
              control={form.control}
              name="ageRangeId"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>{t("variant_field_age_range")}</FormLabel>
                  <Select
                    onValueChange={(v) => field.onChange(v === "__none" ? null : v)}
                    value={field.value ?? "__none"}
                  >
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder={t("variant_field_age_range_placeholder")} />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      <SelectItem value="__none">{t("variant_field_age_range_placeholder")}</SelectItem>
                      {(ageRanges ?? []).map((ar) => (
                        <SelectItem key={ar.id} value={ar.id}>{ar.title}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="rounds"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>
                    {t("variant_field_rounds")}{" "}
                    <span className="text-muted-foreground text-xs">(opt.)</span>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      min="1"
                      step="1"
                      placeholder={t("variant_field_rounds_placeholder")}
                      value={field.value ?? ""}
                      onChange={(e) => {
                        const v = e.target.value
                        field.onChange(v === "" ? null : parseInt(v, 10))
                      }}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={form.control}
              name="timeCap"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>
                    {t("variant_field_timecap")}{" "}
                    <span className="text-muted-foreground text-xs">(opt.)</span>
                  </FormLabel>
                  <FormControl>
                    <Input
                      type="number"
                      min="1"
                      step="1"
                      placeholder={t("variant_field_timecap_placeholder")}
                      value={field.value ?? ""}
                      onChange={(e) => {
                        const v = e.target.value
                        field.onChange(v === "" ? null : parseInt(v, 10))
                      }}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
          </div>

          {/* ── Exercises ── */}
          <div className="space-y-2 pt-1">
            <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
              {t("variant_exercises_label")}
            </p>

            {exercises.length > 0 && (
              <ul className="space-y-1.5">
                {exercises.map((ex, i) => (
                  <li key={i} className="flex items-center gap-2">
                    <span className="text-xs text-muted-foreground w-4 shrink-0">{i + 1}.</span>
                    <span className="text-sm flex-1 min-w-0 truncate">{ex.exerciseName}</span>
                    <select
                      value={ex.metricType}
                      onChange={(e) => updateExerciseMetricType(i, e.target.value)}
                      className="text-xs border rounded px-1.5 py-1 bg-background cursor-pointer shrink-0"
                    >
                      {metricOptions.map((o) => (
                        <option key={o.value} value={o.value}>{o.label}</option>
                      ))}
                    </select>
                    <Input
                      type="number"
                      min="0"
                      step="any"
                      placeholder="—"
                      value={ex.metricValue}
                      onChange={(e) => updateExerciseMetricValue(i, e.target.value)}
                      className="w-16 h-7 text-xs px-2 shrink-0"
                    />
                    <Button
                      type="button"
                      size="icon"
                      variant="ghost"
                      className="size-7 shrink-0 cursor-pointer"
                      onClick={() => removeExercise(i)}
                    >
                      <X className="size-3.5" />
                    </Button>
                  </li>
                ))}
              </ul>
            )}

            {/* Exercise search */}
            <div ref={searchRef} className="relative">
              <Input
                placeholder={t("variant_exercise_search")}
                value={exerciseSearch}
                onChange={(e) => {
                  setExerciseSearch(e.target.value)
                  setShowResults(true)
                }}
                onFocus={() => exerciseSearch.length >= 2 && setShowResults(true)}
                className="h-8 text-sm"
              />
              {showResults && exerciseSearch.length >= 2 && (
                <div
                  className="absolute top-full left-0 right-0 z-50 mt-1 rounded-md border bg-popover shadow-md max-h-48 overflow-y-auto">
                  {!searchResults || searchResults.length === 0 ? (
                    <p className="px-3 py-2 text-xs text-muted-foreground">{t("variant_exercise_no_results")}</p>
                  ) : (
                    searchResults.map((ex) => (
                      <button
                        key={ex.id}
                        type="button"
                        className="w-full text-left px-3 py-1.5 text-sm hover:bg-accent cursor-pointer"
                        onMouseDown={(e) => {
                          e.preventDefault()
                          addExercise(ex.id, ex.title)
                        }}
                      >
                        {ex.title}
                      </button>
                    ))
                  )}
                </div>
              )}
            </div>
          </div>

          {error && <p className="text-xs text-destructive">{error}</p>}

          <div className="flex justify-end gap-2">
            <Button
              type="button"
              size="sm"
              variant="ghost"
              className="cursor-pointer"
              onClick={onCancel}
            >
              {t("variant_cancel")}
            </Button>
            <Button
              type="submit"
              size="sm"
              disabled={isLoading}
              className="cursor-pointer"
            >
              {isLoading ? t("variant_saving") : t("variant_save")}
            </Button>
          </div>
        </form>
      </Form>
    </div>
  )
}

// ─── DeleteVariantButton ──────────────────────────────────────────────────────

interface DeleteVariantButtonProps {
  variantId: string
  onDeleted: () => void
}

function DeleteVariantButton({ variantId, onDeleted }: DeleteVariantButtonProps) {
  const t = useTranslations("admin_wods")
  const [confirming, setConfirming] = useState(false)
  const { mutate, isLoading } = useDeleteWodVariant(variantId)

  async function handleDelete() {
    try {
      await mutate()
      onDeleted()
    } catch {
      setConfirming(false)
    }
  }

  if (confirming) {
    return (
      <div className="flex gap-1 items-center">
        <Button
          type="button"
          size="sm"
          variant="destructive"
          className="h-7 px-2 text-xs cursor-pointer"
          onClick={handleDelete}
          disabled={isLoading}
        >
          {t("variant_confirm_delete")}
        </Button>
        <Button
          type="button"
          size="icon"
          variant="ghost"
          className="size-7 cursor-pointer"
          onClick={() => setConfirming(false)}
        >
          <span className="text-xs">✕</span>
        </Button>
      </div>
    )
  }

  return (
    <Button
      type="button"
      size="icon"
      variant="ghost"
      className="size-7 cursor-pointer"
      onClick={() => setConfirming(true)}
    >
      <Trash2 className="size-3.5" />
    </Button>
  )
}
