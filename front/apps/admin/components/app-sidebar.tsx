"use client"

import { Icon } from "@/components/icon"
import { Logo } from "@/components/logo"
import { NavMain } from "@/components/nav-main"
import { NavUser } from "@/components/nav-user"
import { SidebarNotification } from "@/components/sidebar-notification"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@workspace/ui/components/sidebar"
import { Skeleton } from "@workspace/ui/components/skeleton"
import { buildNav } from "@/lib/nav"
import { useAuth } from "@workspace/api"
import { useTranslations } from "next-intl"
import Link from "next/link"
import * as React from "react"

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  const { user, isLoading } = useAuth()
  const t = useTranslations("nav")
  const navGroups = React.useMemo(() => buildNav(user, t), [user, t])

  return (
    <Sidebar {...props}>
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
              <Link href="/dashboard">
                <Logo className="w-full max-w-full px-6 h-auto text-current group-data-[collapsible=icon]:hidden" />
                <Icon className="hidden text-current group-data-[collapsible=icon]:block" />
              </Link>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <SidebarContent>
        {isLoading ? (
          <NavSkeleton />
        ) : (
          navGroups.map((group) => (
            <NavMain key={group.label} label={group.label} items={group.items} />
          ))
        )}
      </SidebarContent>

      <SidebarFooter>
        <SidebarNotification />
        <NavUser />
      </SidebarFooter>
    </Sidebar>
  )
}

function NavSkeleton() {
  return (
    <div className="flex flex-col gap-4 px-3 py-2">
      {[5, 4, 3].map((count, groupIndex) => (
        <div key={groupIndex} className="flex flex-col gap-1">
          <Skeleton className="mb-2 h-3 w-20" />
          {Array.from({ length: count }).map((_, i) => (
            <Skeleton key={i} className="h-8 w-full rounded-md" />
          ))}
        </div>
      ))}
    </div>
  )
}
