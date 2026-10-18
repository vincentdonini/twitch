import type { BenchmarkDetail } from "@workspace/api"
import { Badge } from "@workspace/ui/components/badge"
import { Card, CardContent, CardHeader, CardTitle } from "@workspace/ui/components/card"
import { MuscleMap } from "@workspace/ui/components/muscle-map"
import { Dumbbell } from "lucide-react"
import { useTranslations } from "next-intl"
import ReactMarkdown from "react-markdown"

interface BenchmarkInformationsTabProps {
  benchmark: BenchmarkDetail
}

export function BenchmarkInformationsTab({ benchmark }: BenchmarkInformationsTabProps) {
  const tc = useTranslations("common.wods")
  const muscles = benchmark.exercise?.muscles ?? []
  const hasInfoContent = benchmark.details || benchmark.rules || benchmark.tips

  return (
    <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
      <div className="lg:col-span-2 space-y-8">
        {(benchmark.details || benchmark.rules || benchmark.tips) && (
          <div className="space-y-8">
            {benchmark.details && <TextSection title={tc("details")} content={benchmark.details} />}
            {benchmark.rules && <TextSection title={tc("rules")} content={benchmark.rules} />}
            {benchmark.tips && <TextSection title={tc("tips")} content={benchmark.tips} />}
          </div>
        )}
        {!hasInfoContent && (
          <p className="text-sm text-muted-foreground italic">
            No additional information available for this benchmark.
          </p>
        )}
      </div>

      <div>
        <Card>
          <CardHeader>
            <CardTitle className="text-base flex items-center gap-2">
              <Dumbbell className="size-4 text-muted-foreground" />
              Muscles worked
            </CardTitle>
          </CardHeader>
          <CardContent className="flex flex-col gap-3">
            {muscles.length === 0 ? (
              <p className="text-sm text-muted-foreground">{tc("no_muscle")}</p>
            ) : (
              <>
                <MuscleMap activeMuscles={muscles} />
                <div className="flex flex-wrap gap-1.5 mt-1">
                  {muscles.map(m => (
                    <Badge key={m.id} variant="outline" className="text-xs">{m.title}</Badge>
                  ))}
                </div>
              </>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  )
}

function TextSection({ title, content }: { title: string; content: string }) {
  return (
    <div className="space-y-3">
      <h2 className="text-xl font-semibold">{title}</h2>
      <div className="prose text-sm text-muted-foreground leading-relaxed">
        <ReactMarkdown>{content}</ReactMarkdown>
      </div>
    </div>
  )
}
