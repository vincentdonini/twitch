import { cn } from "@workspace/ui/lib/utils"

interface PageContainerProps {
  children: React.ReactNode
  size?: "narrow" | "wide" | "full"
  withFooter?: boolean,
  withPadding?: boolean
}

const sizeClasses = {
  narrow: "container mx-auto px-8 lg:max-w-4xl",
  wide: "container mx-auto px-4 sm:px-6 lg:px-8",
  full: "w-full",
}

export function PageContainer(
  {
    children,
    size = "narrow",
    withFooter = true,
    withPadding = true,
  }: PageContainerProps,
) {
  return (
    <section
      className={cn(
        withPadding && "py-12 sm:py-24",
        "bg-muted/50",
        withFooter
          ? "min-h-[calc(100vh-60px-80px)] md:min-h-[calc(100vh-60px-100px)]"
          : "min-h-[calc(100vh-60px)]",
      )}
    >
      <div
        className={cn(
          sizeClasses[size]
        )}
      >
        {children}
      </div>
    </section>
  )
}
