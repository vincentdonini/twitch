"use client"

import {
  Database,
} from "lucide-react"
import { useTranslations } from "next-intl"

const menuSections = [
  {
    title: "muscles",
    items: [
      {
        title: "muscles",
        description: null,
        icon: Database,
        href: "/muscles",
      },
      {
        title: "muscle_areas",
        description: null,
        icon: Database,
        href: "/muscle-areas",
      },
      {
        title: "muscle_groups",
        description: null,
        icon: Database,
        href: "/muscle-groups",
      },
    ],
  },
  {
    title: "exercises",
    items: [
      {
        title: "exercises",
        description: null,
        icon: Database,
        href: "/exercises",
      },
      {
        title: "exercise_categories",
        description: null,
        icon: Database,
        href: "/exercise-categories",
      }
    ],
  },
  {
    title: "equipments",
    items: [
      {
        title: "equipments",
        description: null,
        icon: Database,
        href: "/equipments",
      },
    ],
  },
]

export function MegaMenu() {
  const tc = useTranslations("common")

  return (
    <div className="w-[700px] max-w-[95vw] p-4 sm:p-6 lg:p-8 bg-background">
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 lg:gap-12">
        {menuSections.map((section) => (
          <div key={section.title} className="space-y-4 lg:space-y-6">
            {/* Section Header */}
            <h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wide">
              {tc(section.title)}
            </h3>

            {/* Section Links */}
            <div className="space-y-3 lg:space-y-4">
              {section.items.map((item) => (
                <a
                  key={item.title}
                  href={item.href}
                  className="group block space-y-1 lg:space-y-2 hover:bg-accent rounded-md p-2 lg:p-3 -mx-2 lg:-mx-3 transition-colors my-0"
                >
                  <div className="flex items-center gap-2 lg:gap-3">
                    <item.icon className="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
                    <span className="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
                      {tc(item.title)}
                    </span>
                  </div>
                  {item.description && (
                    <p className="text-xs text-muted-foreground leading-relaxed ml-6 lg:ml-7">
                      {item.description}
                    </p>
                  )}
                </a>
              ))}
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
