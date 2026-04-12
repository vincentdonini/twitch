import { CoachesView } from "./_components/coaches-view"

interface PageProps {
  params: Promise<{ placeId: string }>
}

export default async function Page({ params }: PageProps) {
  const { placeId } = await params

  return (
    <div className="flex flex-col gap-4 px-4 lg:px-6">
      <div>
        <h1 className="text-2xl font-bold tracking-tight">Coachs</h1>
        <p className="text-muted-foreground mt-1">Gérez les coachs associés à cette salle.</p>
      </div>
      <CoachesView placeId={placeId} />
    </div>
  )
}
