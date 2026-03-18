import * as React from "react"
import { Slot } from "@radix-ui/react-slot"
import { cva, type VariantProps } from "class-variance-authority"
import { cn } from "@workspace/ui/lib/utils"

const badgeVariants = cva(
  "inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 [&>svg]:size-3 gap-1 [&>svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden",
  {
    variants: {
      variant: {
        solid: "border-transparent",
        outline: "bg-transparent",
        soft: "border-transparent",
      },
      color: {
        default: "",
        primary: "",
        secondary: "",
        destructive: "",
        success: "",
        warning: "",
        error: "",
        info: "",
      },
    },
    compoundVariants: [
      // solid
      { variant: "solid", color: "default", class: "bg-secondary text-secondary-foreground [a&]:hover:bg-primary/90" },
      { variant: "solid", color: "primary", class: "bg-primary text-primary-foreground [a&]:hover:bg-primary/90" },
      { variant: "solid", color: "secondary", class: "bg-secondary text-secondary-foreground [a&]:hover:bg-secondary/90", },
      { variant: "solid", color: "destructive", class: "bg-destructive text-white [a&]:hover:bg-destructive/90" },
      { variant: "solid", color: "success", class: "bg-success text-success-foreground [a&]:hover:bg-success/90" },
      { variant: "solid", color: "warning", class: "bg-warning text-warning-foreground [a&]:hover:bg-warning/90" },
      { variant: "solid", color: "error", class: "bg-error text-error-foreground [a&]:hover:bg-error/90" },
      { variant: "solid", color: "info", class: "bg-info text-info-foreground [a&]:hover:bg-info/90" },

      // outline
      { variant: "outline", color: "default", class: "border-primary text-primary [a&]:hover:bg-primary/10" },
      { variant: "outline", color: "primary", class: "border-primary text-primary [a&]:hover:bg-primary/10" },
      { variant: "outline", color: "secondary", class: "border-secondary text-secondary-foreground [a&]:hover:bg-secondary/10", },
      { variant: "outline", color: "destructive", class: "border-destructive text-destructive [a&]:hover:bg-destructive/10", },
      { variant: "outline", color: "success", class: "border-success text-success [a&]:hover:bg-success/10" },
      { variant: "outline", color: "warning", class: "border-warning text-warning [a&]:hover:bg-warning/10" },
      { variant: "outline", color: "error", class: "border-error text-error [a&]:hover:bg-error/10" },
      { variant: "outline", color: "info", class: "border-info text-info [a&]:hover:bg-info/10" },

      // soft
      { variant: "soft", color: "default", class: "bg-primary/15 text-primary [a&]:hover:bg-primary/25" },
      { variant: "soft", color: "primary", class: "bg-primary/15 text-primary [a&]:hover:bg-primary/25" },
      { variant: "soft", color: "secondary", class: "bg-secondary text-secondary-foreground [a&]:hover:bg-secondary/70", },
      { variant: "soft", color: "destructive", class: "bg-destructive/15 text-destructive [a&]:hover:bg-destructive/25", },
      { variant: "soft", color: "success", class: "bg-success/15 text-success [a&]:hover:bg-success/25" },
      { variant: "soft", color: "warning", class: "bg-warning/15 text-warning [a&]:hover:bg-warning/25" },
      { variant: "soft", color: "error", class: "bg-error/15 text-error [a&]:hover:bg-error/25" },
      { variant: "soft", color: "info", class: "bg-info/15 text-info [a&]:hover:bg-info/25" },
    ],
    defaultVariants: {
      variant: "solid",
      color: "default",
    },
  },
)

function Badge(
  {
    className,
    variant,
    color,
    asChild = false,
    ...props
  }: React.ComponentProps<"span"> &
    VariantProps<typeof badgeVariants> & { asChild?: boolean },
) {
  const Comp = asChild ? Slot : "span"

  return (
    <Comp
      data-slot="badge"
      className={cn(badgeVariants({ variant, color }), className)}
      {...props}
    />
  )
}

export { Badge, badgeVariants }
