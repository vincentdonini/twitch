import * as React from "react"
import { cn } from "@workspace/ui/lib/utils"
import { ReactNode, CSSProperties } from "react"
import { Separator } from "@workspace/ui/components/separator"

interface StackProps {
  children: ReactNode
  direction?: "vertical" | "horizontal"
  gap?: number | string
  className?: string
  justify?: "start" | "center" | "end" | "between" | "around" | "evenly"
  align?: "start" | "center" | "end" | "stretch"
  style?: CSSProperties
  divider?: ReactNode | boolean
}

function Stack(
  {
    children,
    direction = "vertical",
    gap = 2,
    className,
    justify = "start",
    align = "stretch",
    style,
    divider,
  }: StackProps,
) {
  const justifyMap: Record<string, string> = {
    start: "justify-start",
    center: "justify-center",
    end: "justify-end",
    between: "justify-between",
    around: "justify-around",
    evenly: "justify-evenly",
  }

  const alignMap: Record<string, string> = {
    start: "items-start",
    center: "items-center",
    end: "items-end",
    stretch: "items-stretch",
  }

  const directionClass = direction === "horizontal" ? "flex-row" : "flex-col"

  // Gestion du gap custom
  const gapStyle =
    typeof gap === "string" && !gap.match(/^\d+$/) ? { gap } : {}

  // Transformation des children pour ajouter le divider
  const childrenArray = React.Children.toArray(children)
  const contentWithDivider = divider
    ? childrenArray.flatMap((child, index) => {
      if (index < childrenArray.length - 1) {
        const dividerNode =
          divider === true ? (
            <Separator
              key={`divider-${index}`}
              orientation={direction === "horizontal" ? "vertical" : "horizontal"}
              className={direction === "horizontal" ? "h-full w-px bg-gray-300" : "w-full h-px bg-gray-300"}
            />
          ) : (
            divider
          )
        return [child, dividerNode]
      }
      return [child]
    })
    : childrenArray

  return (
    <div
      className={cn(
        "flex",
        directionClass,
        typeof gap === "number" ? `gap-${gap}` : "",
        justifyMap[justify],
        alignMap[align],
        className,
      )}
      style={{ ...gapStyle, ...style }}
    >
      {contentWithDivider}
    </div>
  )
}

export {
  Stack,
}