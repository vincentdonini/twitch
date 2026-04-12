"use client"

import { env } from "@/lib/env"
import { getInitials } from "@/lib/string"
import { Avatar, AvatarFallback, AvatarImage } from "@workspace/ui/components/avatar"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@workspace/ui/components/dropdown-menu"
import { CircleUserRoundIcon, LayoutDashboardIcon, EllipsisVerticalIcon, LogOutIcon, SettingsIcon } from "lucide-react"
import { useTranslations } from "next-intl"
import Link from "next/link"

interface NavUserProps {
  user: {
    name: string
    email: string
    avatar: string
  }
  onLogout: () => void
}

export function NavUser({ user, onLogout }: NavUserProps) {
  const tc = useTranslations("common")

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <button
          className="flex items-center gap-2 cursor-pointer rounded-lg px-2 py-1 hover:bg-accent transition-colors outline-none">
          <Avatar className="h-8 w-8 rounded-lg grayscale">
            <AvatarImage src={user.avatar} alt={user.name} />
            <AvatarFallback className="rounded-lg">
              {getInitials(user.name)}
            </AvatarFallback>
          </Avatar>
          <div className="grid flex-1 text-left text-sm leading-tight">
            <span className="truncate font-medium">{user.name}</span>
            <span className="truncate text-xs text-muted-foreground">{user.email}</span>
          </div>
          <EllipsisVerticalIcon className="ml-auto size-4" />
        </button>
      </DropdownMenuTrigger>
      <DropdownMenuContent className="min-w-56 rounded-lg" align="end" sideOffset={4}>
        <DropdownMenuLabel className="p-0 font-normal">
          <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <Avatar className="h-8 w-8 rounded-lg">
              <AvatarImage src={user.avatar} alt={user.name} />
              <AvatarFallback className="rounded-lg">{getInitials(user.name)}</AvatarFallback>
            </Avatar>
            <div className="grid flex-1 text-left text-sm leading-tight">
              <span className="truncate font-medium">{user.name}</span>
              <span className="truncate text-xs text-muted-foreground">{user.email}</span>
            </div>
          </div>
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuGroup>
          <DropdownMenuItem asChild>
            <Link
              href={`${env.ADMIN_URL}/dashboard`}
              className="flex items-center gap-2 text-foreground"
            >
              <LayoutDashboardIcon />
              {tc("dashboard")}
            </Link>
          </DropdownMenuItem>

          <DropdownMenuItem asChild>
            <Link
              href={`${env.ADMIN_URL}/dashboard`}
              className="flex items-center gap-2 text-foreground"
            >
              <CircleUserRoundIcon />
              {tc("dashboard")}
            </Link>
          </DropdownMenuItem>

          <DropdownMenuItem asChild>
            <Link
              href={`${env.ADMIN_URL}/settings`}
              className="flex items-center gap-2 text-foreground"
            >
              <SettingsIcon />
              {tc("settings")}
            </Link>
          </DropdownMenuItem>
        </DropdownMenuGroup>
        <DropdownMenuSeparator />
        <DropdownMenuItem className="cursor-pointer text-destructive focus:text-destructive" onClick={onLogout}>
          <LogOutIcon />
          {tc("log_out")}
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  )
}
