export const DEFAULT_SORT = "name"
export const LIMIT = 6

export const FILTER_GROUPS = ["category", "type", "division"] as const

export const FILTER_BASE: Record<string, string> = {
  category: "filters[category.id]",
  type:     "filters[type.id]",
  division: "filters[division.id]",
}

export type SortOptionKey = "sort_name_asc" | "sort_name_desc"

export type SortOption = { id: string; labelKey: SortOptionKey }

export const sortOptions: SortOption[] = [
  { id: "name",  labelKey: "sort_name_asc" },
  { id: "-name", labelKey: "sort_name_desc" },
]

// ---------------------------------------------------------------------------
// Filter state types
// ---------------------------------------------------------------------------

export type OptionState = "include" | "exclude" | "none"

export type FilterGroupState = {
  include: string[]
  exclude: string[]
}

export type SidebarFilterOption = { id: string; label: string; count?: number }

export type SidebarFilterGroup = {
  id: string
  title: string
  isLoading: boolean
  options: SidebarFilterOption[]
  getState: (optionId: string) => OptionState
  onToggle: (optionId: string) => void
}

// ---------------------------------------------------------------------------
// URL serialization
// ---------------------------------------------------------------------------

export type ParsedUrl = {
  filterStates: Record<string, FilterGroupState>
  page: number
  sort: string
  search: string
}

export function parseUrl(params: URLSearchParams): ParsedUrl {
  const filterStates: Record<string, FilterGroupState> = {}
  for (const group of FILTER_GROUPS) {
    const include = params.get(`inc_${group}`)?.split(",").filter(Boolean) ?? []
    const exclude = params.get(`exc_${group}`)?.split(",").filter(Boolean) ?? []
    if (include.length || exclude.length) {
      filterStates[group] = { include, exclude }
    }
  }
  return {
    filterStates,
    page:   Math.max(1, Number(params.get("page") ?? 1)),
    sort:   params.get("sort") ?? DEFAULT_SORT,
    search: params.get("q") ?? "",
  }
}

export function buildUrl(
  filterStates: Record<string, FilterGroupState>,
  page: number,
  sort: string,
  search: string,
): string {
  const params = new URLSearchParams()
  if (search)                params.set("q", search)
  if (sort !== DEFAULT_SORT) params.set("sort", sort)
  if (page > 1)              params.set("page", String(page))
  for (const group of FILTER_GROUPS) {
    const state = filterStates[group]
    if (state?.include.length) params.set(`inc_${group}`, state.include.join(","))
    if (state?.exclude.length) params.set(`exc_${group}`, state.exclude.join(","))
  }
  const qs = params.toString()
  return qs ? `?${qs}` : "?"
}
