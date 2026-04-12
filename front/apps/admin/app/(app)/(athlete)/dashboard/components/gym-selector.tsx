"use client"

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@workspace/ui/components/select"
import { Building2 } from "lucide-react"

interface GymSelectorProps {
  gyms: { placeId: string; placeName: string }[]
  value: string
  onChange: (placeId: string) => void
}

export function GymSelector({ gyms, value, onChange }: GymSelectorProps) {
  if (gyms.length <= 1) return null

  return (
    <div className="flex items-center gap-2">
      <Building2 className="size-4 text-muted-foreground shrink-0" />
      <Select value={value} onValueChange={onChange}>
        <SelectTrigger className="w-52 cursor-pointer">
          <SelectValue />
        </SelectTrigger>
        <SelectContent>
          {gyms.map((g) => (
            <SelectItem key={g.placeId} value={g.placeId}>
              {g.placeName}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
    </div>
  )
}
