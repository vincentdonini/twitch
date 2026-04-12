"use client"

import { ChevronRight, type LucideIcon } from "lucide-react"
import Link from "next/link"
import { usePathname } from "next/navigation"

import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@workspace/ui/components/collapsible"
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuBadge,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from "@workspace/ui/components/sidebar"

export interface NavItem {
    title: string
    url: string
    icon?: LucideIcon
    badge?: string
    target?: "_blank"
    isActive?: boolean
    items?: {
        title: string
        url: string
    }[]
}

export function NavMain({
    label,
    items,
}: {
    label: string
    items: NavItem[]
}) {
    const pathname = usePathname()

    const isParentActive = (item: NavItem): boolean => {
        if (pathname === item.url) return true
        return item.items?.some((sub) => pathname === sub.url) ?? false
    }

    return (
        <SidebarGroup>
            <SidebarGroupLabel>{label}</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) =>
                    item.items?.length ? (
                        <Collapsible
                            key={item.title}
                            asChild
                            defaultOpen={isParentActive(item)}
                            className="group/collapsible"
                        >
                            <SidebarMenuItem>
                                <CollapsibleTrigger asChild>
                                    <SidebarMenuButton tooltip={item.title} className="cursor-pointer">
                                        {item.icon && <item.icon />}
                                        <span>{item.title}</span>
                                        <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        {item.items.map((sub) => (
                                            <SidebarMenuSubItem key={sub.title}>
                                                <SidebarMenuSubButton
                                                    asChild
                                                    className="cursor-pointer"
                                                    isActive={pathname === sub.url}
                                                >
                                                    <Link href={sub.url}>
                                                        <span>{sub.title}</span>
                                                    </Link>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        ))}
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    ) : (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                tooltip={item.title}
                                className="cursor-pointer"
                                isActive={pathname === item.url}
                            >
                                <Link href={item.url} target={item.target}>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                            {item.badge && (
                                <SidebarMenuBadge>{item.badge}</SidebarMenuBadge>
                            )}
                        </SidebarMenuItem>
                    )
                )}
            </SidebarMenu>
        </SidebarGroup>
    )
}
