"use client"

import { Logo } from "@/components/logo"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@workspace/ui/components/dropdown-menu"
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from "@workspace/ui/components/sidebar"
import { useAuth } from "@workspace/api"
import { CircleUser, EllipsisVertical, LogOut } from "lucide-react"
import { useTranslations } from "next-intl"
import Link from "next/link"
import { useRouter } from "next/navigation"

export function NavUser() {
  const { isMobile } = useSidebar()
  const { user, logout } = useAuth()
  const router = useRouter()
  const t = useTranslations("user_menu")

  const displayName = user ? `${user.firstName} ${user.lastName}`.trim() || user.email : "User"
  const displayEmail = user?.email ?? ""

  function handleLogout() {
    logout()
    router.push("/sign-in")
  }

  return (
    <SidebarMenu>
      <SidebarMenuItem>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <SidebarMenuButton
              size="lg"
              className="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground cursor-pointer"
            >
              <div className="flex h-8 w-8 items-center justify-center rounded-lg">
                <Logo size={28} />
              </div>
              <div className="grid flex-1 text-left text-sm leading-tight">
                <span className="truncate font-medium">{displayName}</span>
                <span className="text-muted-foreground truncate text-xs">
                  {displayEmail}
                </span>
              </div>
              <EllipsisVertical className="ml-auto size-4" />
            </SidebarMenuButton>
          </DropdownMenuTrigger>
          <DropdownMenuContent
            className="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
            side={isMobile ? "bottom" : "right"}
            align="end"
            sideOffset={4}
          >
            <DropdownMenuLabel className="p-0 font-normal">
              <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                <div className="h-8 w-8 rounded-lg">
                  <Logo size={28} />
                </div>
                <div className="grid flex-1 text-left text-sm leading-tight">
                  <span className="truncate font-medium">{displayName}</span>
                  <span className="text-muted-foreground truncate text-xs">
                    {displayEmail}
                  </span>
                </div>
              </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
              <DropdownMenuItem asChild className="cursor-pointer">
                <Link href="/settings/account">
                  <CircleUser />
                  {t("account")}
                </Link>
              </DropdownMenuItem>
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem className="cursor-pointer" onClick={handleLogout}>
              <LogOut />
              {t("sign_out")}
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>
    </SidebarMenu>
  )
}
