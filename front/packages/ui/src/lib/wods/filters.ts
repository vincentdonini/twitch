import type { FilterGroupState } from "@workspace/ui/components/wod-filters-sidebar/types"
import type { OptionState } from "@workspace/ui/components/wod-filters-sidebar/types"

// ─── Constantes ───────────────────────────────────────────────────────────────

export const DefaultSort = "name"
export const PageLimit = 6

export const WodFilterGroup = [
  "category", "type", "division", "exercise", "exerciseCategory", "equipment",
  "muscle", "muscleArea", "muscleGroup", "muscleSegment",
] as const
export type WodFilterGroup = typeof WodFilterGroup[number]

export const WodFilterBase: Record<WodFilterGroup, string> = {
  category: "filters[category.id]",
  type: "filters[type.id]",
  division: "filters[division.id]",
  exercise: "filters[exercise.id]",
  exerciseCategory: "filters[exerciseCategory.id]",
  equipment: "filters[equipment.id]",
  muscle:        "filters[muscle.id]",
  muscleArea:    "filters[muscleArea.id]",
  muscleGroup:   "filters[muscleGroup.id]",
  muscleSegment: "filters[muscleSegment.id]",
}

// ─── Enums ────────────────────────────────────────────────────────────────────

export const WodSortOption = {
  NameAsc: "name",
  NameDesc: "-name",
} as const
export type WodSortOption = typeof WodSortOption[keyof typeof WodSortOption]

// ─── Types ───────────────────────────────────────────────────────────────────

export type WodSortOptionItem = {
  id: WodSortOption
  labelKey: string
}

export type WodSidebarFilterOption = {
  id: string
  label: string
  count?: number
}

export type WodSidebarFilterGroup = {
  id: WodFilterGroup
  title: string
  isLoading: boolean
  options: WodSidebarFilterOption[]
  getState: (optionId: string) => OptionState
  onToggle: (optionId: string) => void
  searchable?: boolean
  selectedIds?: { include: string[]; exclude: string[] }
  labelCache?: Record<string, string>
  onLabelsDiscovered?: (labels: Record<string, string>) => void
}

export type WodParsedUrl = {
  filterStates: Partial<Record<WodFilterGroup, FilterGroupState>>
  page: number
  sort: WodSortOption
  search: string
}

// ─── Données statiques ────────────────────────────────────────────────────────

export const WodSortOptions: WodSortOptionItem[] = [
  { id: WodSortOption.NameAsc, labelKey: "sort_name_asc" },
  { id: WodSortOption.NameDesc, labelKey: "sort_name_desc" },
]

// ─── Fonctions ────────────────────────────────────────────────────────────────

export function parseWodUrl(params: URLSearchParams): WodParsedUrl {
  const filterStates: Partial<Record<WodFilterGroup, FilterGroupState>> = {}
  for (const group of WodFilterGroup) {
    const include = params.get(`inc_${group}`)?.split(",").filter(Boolean) ?? []
    const exclude = params.get(`exc_${group}`)?.split(",").filter(Boolean) ?? []
    if (include.length || exclude.length) {
      filterStates[group] = { include, exclude }
    }
  }
  return {
    filterStates,
    page: Math.max(1, Number(params.get("page") ?? 1)),
    sort: (params.get("sort") as WodSortOption) ?? DefaultSort,
    search: params.get("q") ?? "",
  }
}

export function buildWodUrl(
  filterStates: Partial<Record<WodFilterGroup, FilterGroupState>>,
  page: number,
  sort: WodSortOption,
  search: string,
): string {
  const params = new URLSearchParams()
  if (search) params.set("q", search)
  if (sort !== DefaultSort) params.set("sort", sort)
  if (page > 1) params.set("page", String(page))
  for (const group of WodFilterGroup) {
    const state = filterStates[group]
    if (state?.include.length) params.set(`inc_${group}`, state.include.join(","))
    if (state?.exclude.length) params.set(`exc_${group}`, state.exclude.join(","))
  }
  const qs = params.toString()
  return qs ? `?${qs}` : "?"
}
