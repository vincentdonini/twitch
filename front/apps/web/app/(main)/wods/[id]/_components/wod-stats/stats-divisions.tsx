import { Badge } from "@workspace/ui/components/badge"
import { useTranslations } from "next-intl"

interface StatsDivisionsProps {
  divisions: string[]
}

export function StatsDivisions({ divisions }: StatsDivisionsProps) {
  const t = useTranslations("wods")

  return (
    <div className="space-y-1.5">
      <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
        {t("divisions")}
      </p>
      <div className="flex flex-wrap gap-1.5">
        {divisions.map(d => (
          <Badge key={d} color="secondary" className="text-xs">
            {d}
          </Badge>
        ))}
      </div>
    </div>
  )
}
