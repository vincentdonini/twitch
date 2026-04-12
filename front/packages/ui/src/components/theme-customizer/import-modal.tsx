"use client"

import React from "react"
import { Button } from "@workspace/ui/components/button"
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from "@workspace/ui/components/dialog"
import { Textarea } from "@workspace/ui/components/textarea"
import type { ImportedTheme } from "@workspace/ui/types/theme-customizer"

interface ImportModalProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  onImport: (theme: ImportedTheme) => void
}

export function ImportModal({ open, onOpenChange, onImport }: ImportModalProps) {
  const [importText, setImportText] = React.useState("")

  const processImport = () => {
    try {
      if (!importText.trim()) return

      const lightTheme: Record<string, string> = {}
      const darkTheme: Record<string, string> = {}

      const cssText = importText.replace(/\/\*[\s\S]*?\*\//g, "")

      const rootMatch = cssText.match(/:root\s*\{([^}]+)\}/)
      if (rootMatch) {
        const variableMatches = rootMatch[1].matchAll(/--([^:]+):\s*([^;]+);/g)
        for (const match of variableMatches) {
          lightTheme[match[1].trim()] = match[2].trim()
        }
      }

      const darkMatch = cssText.match(/\.dark\s*\{([^}]+)\}/)
      if (darkMatch) {
        const variableMatches = darkMatch[1].matchAll(/--([^:]+):\s*([^;]+);/g)
        for (const match of variableMatches) {
          darkTheme[match[1].trim()] = match[2].trim()
        }
      }

      onImport({ light: lightTheme, dark: darkTheme })
      onOpenChange(false)
      setImportText("")
    } catch (error) {
      console.error("Error importing theme:", error)
    }
  }

  return (
    <Dialog open={open} onOpenChange={onOpenChange} modal={true}>
      <DialogContent className="max-w-4xl w-[90vw]">
        <DialogHeader>
          <DialogTitle>Import Custom CSS</DialogTitle>
          <DialogDescription>
            Paste your CSS theme below. Include both <code>:root</code> (light mode) and{" "}
            <code>.dark</code> (dark mode) sections with CSS variables like <code>--primary</code>,{" "}
            <code>--background</code>, etc.
          </DialogDescription>
        </DialogHeader>
        <div className="space-y-4">
          <Textarea
            className="max-h-[400px] min-h-[300px] font-mono text-sm resize-none"
            placeholder={`:root {
  --background: 0 0% 100%;
  --primary: #3e2723;
}
.dark {
  --background: 222.2 84% 4.9%;
  --primary: rgb(46, 125, 50);
}`}
            value={importText}
            onChange={(e) => setImportText(e.target.value)}
          />
          <div className="flex gap-2 justify-end">
            <Button variant="outline" onClick={() => onOpenChange(false)} className="cursor-pointer">
              Cancel
            </Button>
            <Button onClick={processImport} disabled={!importText.trim()} className="cursor-pointer">
              Import Theme
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  )
}
