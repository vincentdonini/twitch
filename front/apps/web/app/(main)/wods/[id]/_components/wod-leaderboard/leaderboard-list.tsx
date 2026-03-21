"use client"

import { Time } from "@/lib/time"
import type { LeaderboardEntry } from "@workspace/api"
import { Avatar, AvatarFallback } from "@workspace/ui/components/avatar"
import { Stack } from "@workspace/ui/components/stack"
import { Crown } from "lucide-react"

function score(e: LeaderboardEntry): string {
  if (e.time != null) return Time.formatSecondsToDynamicHms(e.time)
  if (e.repetitions != null) return `${e.repetitions} reps`
  if (e.weight != null) return `${e.weight} kg`
  return "—"
}

function initials(e: LeaderboardEntry): string {
  return `${e.user.firstName[0] ?? ""}${e.user.lastName[0] ?? ""}`.toUpperCase()
}

function fullName(e: LeaderboardEntry): string {
  return `${e.user.firstName} ${e.user.lastName}`
}

function performedDate(e: LeaderboardEntry): string {
  return new Date(e.performedAt).toLocaleDateString("fr-FR", { day: "numeric", month: "short" })
}

export function LeaderboardList({ entries }: { entries: LeaderboardEntry[] }) {
  const [first, ...rest] = entries

  return (
    <Stack
      gap={2}
    >
      {/* 1st place — podium card */}
      {first && (
        <div
          className="mb-2 relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-400 via-yellow-400 to-orange-400 p-4 text-amber-950"
        >
          <div className="absolute -right-3 -top-3 opacity-10">
            <Crown className="size-20" />
          </div>
          <div className="flex items-center gap-3">
            <div className="relative">
              <Avatar className="size-12 ring-2 ring-amber-200 shadow">
                <AvatarFallback className="bg-amber-200 text-amber-900 font-bold text-sm">
                  {initials(first)}
                </AvatarFallback>
              </Avatar>
              <span
                className="absolute -bottom-1 -right-1 flex size-5 items-center justify-center rounded-full bg-amber-900 text-[10px] font-bold text-amber-100 shadow"
              >
                #1
              </span>
            </div>
            <div className="min-w-0 flex-1">
              <p className="font-bold text-sm truncate">{fullName(first)}</p>
              <p className="text-xs text-amber-800">{performedDate(first)}</p>
            </div>
            <div className="text-right shrink-0">
              <p className="text-xl font-black leading-none">{score(first)}</p>
              <Crown className="size-3.5 mt-1 ml-auto" />
            </div>
          </div>
        </div>
      )}

      {/* 2nd+ — compact table */}
      {entries.length > 1 && (
        <table className="w-full text-sm">
          <tbody>
          {rest.map((entry, index) => {
            const realIndex = index + 1
            const previous = entries[realIndex - 1]

            const displayRank = entry.rank
            const isSameRank = entry.rank === previous?.rank

            return (
              <tr key={entry.id} className="border-b border-border/40 last:border-0">
                <td className="py-2 pl-2 whitespace-nowrap">
              <span className="text-xs font-mono text-muted-foreground">
                {isSameRank ? "—" : "#" + displayRank}
              </span>
                </td>
                <td className="py-2 px-2 w-full max-w-0">
                  <div className="flex items-center gap-2 overflow-hidden">
                    <Avatar className="size-7 shrink-0">
                      <AvatarFallback className="text-[10px] font-semibold bg-muted">
                        {initials(entry)}
                      </AvatarFallback>
                    </Avatar>
                    <span className="font-medium truncate">{fullName(entry)}</span>
                  </div>
                </td>
                <td className="py-2 pr-2 whitespace-nowrap text-right">
                  <span className="font-bold tabular-nums">{score(entry)}</span>
                </td>
              </tr>
            )
          })}
          </tbody>
        </table>
      )}
    </Stack>
  )
}
