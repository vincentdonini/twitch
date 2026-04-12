import type { ReactNode } from "react"

export const OptionState = {
  Include: "include",
  Exclude: "exclude",
  None: "none",
} as const
export type OptionState = (typeof OptionState)[keyof typeof OptionState]

export type FilterGroupState = {
  include: string[]
  exclude: string[]
}

export interface FilterGroup {
  id: string
  title: string
  isLoading: boolean
  /** Options for standard (non-searchable) groups */
  options?: { id: string; label: string; count?: number }[]
  getState: (optionId: string) => OptionState
  onToggle: (optionId: string) => void
  /** For searchable groups: render the filter content (API calls live in the app) */
  renderContent?: () => ReactNode
}

export interface FilterGroupSection {
  id: string
  title: string
  groups: FilterGroup[]
}

export type FilterItem = FilterGroup | FilterGroupSection

export function isFilterGroupSection(item: FilterItem): item is FilterGroupSection {
  return 'groups' in item
}
