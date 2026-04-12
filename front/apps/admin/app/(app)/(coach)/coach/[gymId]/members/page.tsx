import { MembersView } from "./_components/members-view"

interface PageProps {
  params: Promise<{ gymId: string }>
}

export default async function Page({ params }: PageProps) {
  const { gymId } = await params

  return (
    <div className="flex flex-col gap-4 px-4 lg:px-6">
      <div>
        <h1 className="text-2xl font-bold tracking-tight">Membres</h1>
        <p className="text-muted-foreground mt-1">Liste des athlètes inscrits à cette salle.</p>
      </div>
      <MembersView gymId={gymId} />
    </div>
  )
}
