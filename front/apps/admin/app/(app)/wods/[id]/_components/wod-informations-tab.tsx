import { type WodDetail } from "@workspace/api"
import { Separator } from "@workspace/ui/components/separator"
import { WodPattern } from "@workspace/ui/components/wod-pattern"
import { WodStatsCard } from "@workspace/ui/components/wod-stats-card"
import { useTranslations } from "next-intl"
import ReactMarkdown from "react-markdown"

interface WodInformationsTabProps {
  wod: WodDetail
}

export function WodInformationsTab({ wod }: WodInformationsTabProps) {
  const tc = useTranslations("common.wods")

  const exercises = uniqueExercises(wod)

  return (
    <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">

      {/* Main — pattern + text sections */}
      <div className="lg:col-span-2 space-y-8">
        {wod.variants.length > 0 && (
          <WodPattern variants={wod.variants} />
        )}

        {(wod.details || wod.rules || wod.tips) && (
          <div className="space-y-8">
            <Separator />
            {wod.details && <TextSection title={tc("details")} content={wod.details} />}
            {wod.rules && <TextSection title={tc("rules")} content={wod.rules} />}
            {wod.tips && <TextSection title={tc("tips")} content={wod.tips} />}
          </div>
        )}

        {!wod.variants.length && !wod.details && !wod.rules && !wod.tips && (
          <p className="text-sm text-muted-foreground italic">
            No additional information available for this WOD.
          </p>
        )}
      </div>

      {/* Sidebar — stats */}
      <div>
        <WodStatsCard
          exercises={exercises}
          exerciseCategories={uniqueExerciseCategories(exercises)}
          equipments={uniqueEquipments(exercises)}
          muscles={uniqueMuscles(exercises)}
          divisions={uniqueDivisions(wod)}
        />
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

function uniqueExercises(wod: WodDetail) {
  return [...new Map(wod.variants.flatMap(v => v.exercises.map(e => [e.exercise.id, e.exercise]))).values()]
}
function uniqueExerciseCategories(exercises: ReturnType<typeof uniqueExercises>) {
  return [...new Map(exercises.filter(e => e.exerciseCategory != null).map(e => [e.exerciseCategory!.id, e.exerciseCategory!])).values()]
}
function uniqueEquipments(exercises: ReturnType<typeof uniqueExercises>) {
  return [...new Map(exercises.filter(e => e.equipment != null).map(e => [e.equipment!.id, e.equipment!])).values()]
}
function uniqueMuscles(exercises: ReturnType<typeof uniqueExercises>) {
  return [...new Map(exercises.flatMap(e => e.muscles).map(m => [m.id, m])).values()]
}
function uniqueDivisions(wod: WodDetail): string[] {
  return [...new Map(wod.variants.map(v => [v.division.id, v.division.title])).values()]
}
