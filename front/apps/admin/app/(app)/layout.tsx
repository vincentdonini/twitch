"use client"

import React from "react"
import { AppSidebar } from "@/components/app-sidebar"
import { SiteHeader } from "@/components/site-header"
import { SiteFooter } from "@/components/site-footer"
import { ThemeCustomizer, ThemeCustomizerTrigger } from "@workspace/ui/components/theme-customizer"
import { BaseLayout } from "@workspace/ui/components/layouts/base-layout"
import { useSidebarConfig } from "@/hooks/use-sidebar-config"
import { colorThemes, tweakcnThemes } from "@/config/theme-data"

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const [themeCustomizerOpen, setThemeCustomizerOpen] = React.useState(false)
  const { config } = useSidebarConfig()

  return (
    <BaseLayout
      sidebar={
        <AppSidebar
          variant={config.variant}
          collapsible={config.collapsible}
          side={config.side}
        />
      }
      header={<SiteHeader />}
      footer={<SiteFooter />}
      themeCustomizer={
        <>
          <ThemeCustomizerTrigger onClick={() => setThemeCustomizerOpen(true)} />
          <ThemeCustomizer
            open={themeCustomizerOpen}
            onOpenChange={setThemeCustomizerOpen}
            colorThemes={colorThemes}
            tweakcnThemes={tweakcnThemes}
          />
        </>
      }
    >
      {children}
    </BaseLayout>
  )
}
