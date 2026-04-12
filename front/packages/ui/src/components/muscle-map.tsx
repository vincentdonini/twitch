"use client"

import { useEffect, useRef } from "react"
import type { MuscleSummary } from "@workspace/api"
import { MUSCLE_SLUG_TO_PATHS } from "./muscle-map-data"
import { MUSCLES_BACK_SVG, MUSCLES_FRONT_SVG } from "./muscle-svg-content"

interface MuscleMapProps {
  activeMuscles: MuscleSummary[]
  /** Base fill color for inactive paths */
  inactiveColor?: string
  /** Highlight fill color for active paths */
  activeColor?: string
}

const DEFAULT_INACTIVE = "#404040"
const DEFAULT_ACTIVE = "#d42127"

/**
 * CSS custom properties (var(--x)) are not resolved in SVG inline styles.
 * We read the computed value from the DOM and build a real color string.
 */
function resolveColor(el: Element, value: string): string {
  if (!value.includes("var(")) return value

  const varName = value.match(/var\((--[^),\s]+)/)?.[1]
  if (!varName) return value

  const raw = getComputedStyle(el).getPropertyValue(varName).trim()
  if (!raw) return value

  if (/^(oklch|hsl|rgb|color)\(/.test(raw)) return raw

  const fn = value.replace(/var\([^)]+\)/, raw)
  return fn
}

function applyHighlights(
  container: HTMLDivElement | null,
  side: "front" | "back",
  activeMuscles: MuscleSummary[],
  inactiveColor: string,
  activeColor: string,
) {
  if (!container) return
  const svg = container.querySelector("svg")
  if (!svg) return

  const resolvedActive = resolveColor(container, activeColor)
  const resolvedInactive = resolveColor(container, inactiveColor)

  const activePaths = new Set<string>(
    activeMuscles.flatMap(m => {
      const paths = MUSCLE_SLUG_TO_PATHS[m.slug]
      return paths ? (side === "front" ? paths.front : paths.back) : []
    }),
  )

  svg.querySelectorAll<SVGPathElement>("path[id]").forEach(path => {
    path.setAttribute("fill", activePaths.has(path.id) ? resolvedActive : resolvedInactive)
  })
}

function SvgPanel({
  svgContent,
  side,
  activeMuscles,
  inactiveColor,
  activeColor,
}: {
  svgContent: string
  side: "front" | "back"
  activeMuscles: MuscleSummary[]
  inactiveColor: string
  activeColor: string
}) {
  const ref = useRef<HTMLDivElement>(null)

  useEffect(() => {
    applyHighlights(ref.current, side, activeMuscles, inactiveColor, activeColor)
  }, [activeMuscles, side, inactiveColor, activeColor])

  return (
    <div
      ref={ref}
      // eslint-disable-next-line react/no-danger
      dangerouslySetInnerHTML={{ __html: svgContent }}
      className="w-full h-full [&>svg]:w-full [&>svg]:h-full"
    />
  )
}

export function MuscleMap({
  activeMuscles,
  inactiveColor = DEFAULT_INACTIVE,
  activeColor = DEFAULT_ACTIVE,
}: MuscleMapProps) {
  return (
    <div className="flex gap-2 w-full">
      {/* Front view */}
      <div className="flex flex-col items-center gap-1 flex-1">
        <div className="w-full aspect-[1/2]">
          <SvgPanel
            svgContent={MUSCLES_FRONT_SVG}
            side="front"
            activeMuscles={activeMuscles}
            inactiveColor={inactiveColor}
            activeColor={activeColor}
          />
        </div>
        <span className="text-[10px] font-medium text-muted-foreground uppercase tracking-widest">
          Front
        </span>
      </div>

      {/* Back view */}
      <div className="flex flex-col items-center gap-1 flex-1">
        <div className="w-full aspect-[1/2]">
          <SvgPanel
            svgContent={MUSCLES_BACK_SVG}
            side="back"
            activeMuscles={activeMuscles}
            inactiveColor={inactiveColor}
            activeColor={activeColor}
          />
        </div>
        <span className="text-[10px] font-medium text-muted-foreground uppercase tracking-widest">
          Back
        </span>
      </div>
    </div>
  )
}
