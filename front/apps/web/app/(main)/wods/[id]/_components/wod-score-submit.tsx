"use client"

import { WodScoreDialog } from "@/app/(main)/wods/[id]/_components/wod-score-dialog"
import { type WodDetail } from "@workspace/api"
import { useState } from "react"

interface WodScoreSubmitProps {
  wod: WodDetail
  onSuccess?: () => void
  children: (props: { open: () => void }) => React.ReactNode
}

export function WodScoreSubmit(
  {
    wod,
    onSuccess,
    children,
  }: WodScoreSubmitProps,
) {
  const [dialogOpen, setDialogOpen] = useState(false)

  return (
    <>
      {children({ open: () => setDialogOpen(true) })}
      <WodScoreDialog
        open={dialogOpen}
        onOpenChange={setDialogOpen}
        wod={wod}
        onSuccess={onSuccess}
      />
    </>
  )
}
