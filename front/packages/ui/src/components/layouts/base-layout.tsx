"use client"

import * as React from "react"
import { useSidebarConfig } from "@workspace/ui/contexts/sidebar-context"
import { SidebarInset, SidebarProvider } from "@workspace/ui/components/sidebar"

interface BaseLayoutProps {
  children: React.ReactNode
  sidebar: React.ReactNode
  header: React.ReactNode
  footer?: React.ReactNode
  themeCustomizer?: React.ReactNode
}

export function BaseLayout({ children, sidebar, header, footer, themeCustomizer }: BaseLayoutProps) {
  const { config } = useSidebarConfig()

  return (
    <SidebarProvider
      style={
        {
          "--sidebar-width": "16rem",
          "--sidebar-width-icon": "3rem",
          "--header-height": "calc(var(--spacing) * 14)",
        } as React.CSSProperties
      }
      className={config.collapsible === "none" ? "sidebar-none-mode" : ""}
    >
      {config.side === "left" ? (
        <>
          {sidebar}
          <SidebarInset>
            {header}
            <div className="flex flex-1 flex-col">
              <div className="@container/main flex flex-1 flex-col gap-2">
                <div className="flex flex-col gap-4 py-4 md:gap-6 md:py-6">{children}</div>
              </div>
            </div>
            {footer}
          </SidebarInset>
        </>
      ) : (
        <>
          <SidebarInset>
            {header}
            <div className="flex flex-1 flex-col">
              <div className="@container/main flex flex-1 flex-col gap-2">
                <div className="flex flex-col gap-4 py-4 md:gap-6 md:py-6">{children}</div>
              </div>
            </div>
            {footer}
          </SidebarInset>
          {sidebar}
        </>
      )}
      {themeCustomizer}
    </SidebarProvider>
  )
}
