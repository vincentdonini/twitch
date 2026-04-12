"use client"

import React from "react"
import { Layout, Palette, RotateCcw, Settings, X } from "lucide-react"
import { Button } from "@workspace/ui/components/button"
import { Sheet, SheetContent, SheetDescription, SheetHeader, SheetTitle } from "@workspace/ui/components/sheet"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@workspace/ui/components/tabs"
import { useThemeManager } from "@workspace/ui/hooks/use-theme-manager"
import { useSidebarConfig } from "@workspace/ui/contexts/sidebar-context"
import { ThemeTab } from "./theme-tab"
import { LayoutTab } from "./layout-tab"
import { ImportModal } from "./import-modal"
import { cn } from "@workspace/ui/lib/utils"
import type { ColorTheme, ImportedTheme } from "@workspace/ui/types/theme-customizer"

interface ThemeCustomizerProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  colorThemes: ColorTheme[]
  tweakcnThemes: ColorTheme[]
}

export function ThemeCustomizer({ open, onOpenChange, colorThemes, tweakcnThemes }: ThemeCustomizerProps) {
  const { applyImportedTheme, isDarkMode, resetTheme, applyRadius, setBrandColorsValues, applyTheme, applyTweakcnTheme } =
    useThemeManager()
  const { config: sidebarConfig, updateConfig: updateSidebarConfig } = useSidebarConfig()

  const [activeTab, setActiveTab] = React.useState("theme")
  const [selectedTheme, setSelectedTheme] = React.useState("red")
  const [selectedTweakcnTheme, setSelectedTweakcnTheme] = React.useState("")
  const [selectedRadius, setSelectedRadius] = React.useState("0.5rem")
  const [importModalOpen, setImportModalOpen] = React.useState(false)
  const [importedTheme, setImportedTheme] = React.useState<ImportedTheme | null>(null)

  const handleReset = () => {
    setSelectedTheme("default")
    setSelectedTweakcnTheme("")
    setSelectedRadius("0.5rem")
    setImportedTheme(null)
    setBrandColorsValues({})
    resetTheme()
    applyRadius("0.5rem")
    updateSidebarConfig({ variant: "inset", collapsible: "offcanvas", side: "left" })
  }

  const handleImport = (themeData: ImportedTheme) => {
    setImportedTheme(themeData)
    setSelectedTheme("")
    setSelectedTweakcnTheme("")
    applyImportedTheme(themeData, isDarkMode)
  }

  // Re-apply on dark/light toggle
  React.useEffect(() => {
    if (importedTheme) {
      applyImportedTheme(importedTheme, isDarkMode)
    } else if (selectedTheme) {
      const preset = colorThemes.find((t) => t.value === selectedTheme)?.preset
      if (preset) applyTheme(preset, isDarkMode)
    } else if (selectedTweakcnTheme) {
      const preset = tweakcnThemes.find((t) => t.value === selectedTweakcnTheme)?.preset
      if (preset) applyTweakcnTheme(preset, isDarkMode)
    }
  }, [isDarkMode]) // eslint-disable-line react-hooks/exhaustive-deps

  return (
    <>
      <Sheet open={open} onOpenChange={onOpenChange} modal={false}>
        <SheetContent
          side={sidebarConfig.side === "left" ? "right" : "left"}
          className="w-[400px] p-0 gap-0 pointer-events-auto [&>button]:hidden overflow-hidden flex flex-col"
          onInteractOutside={(e) => {
            if (importModalOpen) e.preventDefault()
          }}
        >
          <SheetHeader className="space-y-0 p-4 pb-2">
            <div className="flex items-center gap-2">
              <div className="p-2 bg-primary/10 rounded-lg">
                <Settings className="h-4 w-4" />
              </div>
              <SheetTitle className="text-lg font-semibold">Customizer</SheetTitle>
              <div className="ml-auto flex items-center gap-2">
                <Button variant="outline" size="icon" onClick={handleReset} className="cursor-pointer h-8 w-8">
                  <RotateCcw className="h-4 w-4" />
                </Button>
                <Button
                  variant="outline"
                  size="icon"
                  onClick={() => onOpenChange(false)}
                  className="cursor-pointer h-8 w-8"
                >
                  <X className="h-4 w-4" />
                </Button>
              </div>
            </div>
            <SheetDescription className="sr-only">Customize the theme and layout of your dashboard.</SheetDescription>
          </SheetHeader>

          <div className="flex-1 overflow-y-auto">
            <Tabs value={activeTab} onValueChange={setActiveTab} className="h-full flex flex-col">
              <div className="py-2">
                <TabsList className="grid w-full grid-cols-2 rounded-none h-12 p-1.5">
                  <TabsTrigger value="theme" className="cursor-pointer data-[state=active]:bg-background">
                    <Palette className="h-4 w-4 mr-1" /> Theme
                  </TabsTrigger>
                  <TabsTrigger value="layout" className="cursor-pointer data-[state=active]:bg-background">
                    <Layout className="h-4 w-4 mr-1" /> Layout
                  </TabsTrigger>
                </TabsList>
              </div>

              <TabsContent value="theme" className="flex-1 mt-0">
                <ThemeTab
                  colorThemes={colorThemes}
                  tweakcnThemes={tweakcnThemes}
                  selectedTheme={selectedTheme}
                  setSelectedTheme={setSelectedTheme}
                  selectedTweakcnTheme={selectedTweakcnTheme}
                  setSelectedTweakcnTheme={setSelectedTweakcnTheme}
                  selectedRadius={selectedRadius}
                  setSelectedRadius={setSelectedRadius}
                  setImportedTheme={setImportedTheme}
                  onImportClick={() => setImportModalOpen(true)}
                />
              </TabsContent>

              <TabsContent value="layout" className="flex-1 mt-0">
                <LayoutTab />
              </TabsContent>
            </Tabs>
          </div>
        </SheetContent>
      </Sheet>

      <ImportModal open={importModalOpen} onOpenChange={setImportModalOpen} onImport={handleImport} />
    </>
  )
}

export function ThemeCustomizerTrigger({ onClick }: { onClick: () => void }) {
  const { config: sidebarConfig } = useSidebarConfig()

  return (
    <Button
      onClick={onClick}
      size="icon"
      className={cn(
        "fixed top-1/2 -translate-y-1/2 h-12 w-12 rounded-full shadow-lg z-50 bg-primary hover:bg-primary/90 text-primary-foreground cursor-pointer",
        sidebarConfig.side === "left" ? "right-4" : "left-4",
      )}
    >
      <Settings className="h-5 w-5" />
    </Button>
  )
}
