"use client"

import { useState } from "react"
import Link from "next/link"
import { ChevronDown, LayoutDashboard, Menu, X } from "lucide-react"
import { Button } from "@workspace/ui/components/button"
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
} from "@workspace/ui/components/navigation-menu"
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from "@workspace/ui/components/sheet"
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from "@workspace/ui/components/collapsible"
import { MegaMenu } from "@workspace/ui/components/landing/mega-menu"
import { LocaleSwitcher } from "@/ui/components/locale-switcher"
import { ThemeToggle } from "@workspace/ui/components/theme-toggle"
import { useTranslations } from "next-intl"
import { env } from "@/lib/env"
import { useAuth } from "@workspace/api"
import { NavUser } from "@/ui/components/nav-user"

const navigationItems = [
  { name: "home", href: "/" },
  { name: "wods", href: "/wods" },
  { name: "resources", href: "/resources", hasMegaMenu: true },
  { name: "contact", href: "/contact" },
]

// Solutions menu items for mobile
const resourcesItems = [
  { title: "muscles" },
  { name: "muscles", href: "/muscles" },
  { name: "muscle_areas", href: "/muscle-areas" },
  { name: "muscle_groups", href: "/muscle-groups" },
  { title: "exercises" },
  { name: "exercises", href: "/exercises" },
  { title: "equipments" },
  { name: "equipments", href: "/equipments" },
]

// Smooth scroll function
const smoothScrollTo = (targetId: string) => {
  if (targetId.startsWith("#")) {
    const element = document.querySelector(targetId)
    if (element) {
      element.scrollIntoView({
        behavior: "smooth",
        block: "start",
      })
    }
  }
}

export function Navbar() {
  const tc = useTranslations("common")
  const { isAuthenticated, isLoading, user, logout } = useAuth()

  const [isOpen, setIsOpen] = useState(false)
  const [resourcesOpen, setSolutionsOpen] = useState(false)

  return (
    <header
      className="h-[60px] sticky top-0 z-50 w-full border-b bg-background/80 backdrop-blur-xl supports-[backdrop-filter]:bg-background/60">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 flex h-full items-center justify-between">

        <div className="flex items-center space-x-12">
          <Link
            href={env.APP_URL}
            className="flex items-center space-x-2 cursor-pointer"
            target="_blank" rel="noopener noreferrer"
          >
            <span className="font-black text-4xl uppercase">
              {env.APP_NAME}
            </span>
          </Link>

          <NavigationMenu className="hidden xl:flex">
            <NavigationMenuList>
              {navigationItems.map((item) => (
                <NavigationMenuItem key={item.name}>
                  {item.hasMegaMenu ? (
                    <>
                      <NavigationMenuTrigger
                        className="bg-transparent hover:bg-transparent focus:bg-transparent data-[active]:bg-transparent data-[state=open]:bg-transparent px-4 py-2 text-sm font-medium transition-colors hover:text-primary focus:text-primary cursor-pointer">
                        {tc(item.name)}
                      </NavigationMenuTrigger>
                      <NavigationMenuContent>
                        <MegaMenu />
                      </NavigationMenuContent>
                    </>
                  ) : (
                    <NavigationMenuLink
                      className="group inline-flex h-10 w-max items-center justify-center px-4 py-2 text-sm font-medium transition-colors hover:text-primary focus:text-primary focus:outline-none cursor-pointer"
                      onClick={(e: React.MouseEvent) => {
                        e.preventDefault()
                        if (item.href.startsWith("#")) {
                          smoothScrollTo(item.href)
                        } else {
                          window.location.href = item.href
                        }
                      }}
                    >
                      {tc(item.name)}
                    </NavigationMenuLink>
                  )}
                </NavigationMenuItem>
              ))}
            </NavigationMenuList>
          </NavigationMenu>
        </div>

        <div className="hidden xl:flex items-center space-x-2">
          <ThemeToggle />
          <LocaleSwitcher />
          {!isLoading && (isAuthenticated ? (
            <NavUser
              user={{
                name: user ? `${user.firstName} ${user.lastName}` : "",
                email: user?.email ?? "",
                avatar: "",
              }}
              onLogout={logout}
            />
          ) : (
            <>
              <Button variant="ghost" asChild className="cursor-pointer">
                <Link href="/login">Sign In</Link>
              </Button>
              <Button asChild className="cursor-pointer">
                <Link href="/register">Get Started</Link>
              </Button>
            </>
          ))}
        </div>

        <Sheet open={isOpen} onOpenChange={setIsOpen}>
          <SheetTrigger asChild className="xl:hidden">
            <Button variant="ghost" size="icon" className="cursor-pointer">
              <Menu className="h-5 w-5" />
              <span className="sr-only">Toggle menu</span>
            </Button>
          </SheetTrigger>
          <SheetContent
            side="right"
            className="w-full sm:w-[400px] p-0 gap-0 [&>button]:hidden overflow-hidden flex flex-col"
          >
            <div className="flex flex-col h-full">
              <SheetHeader className="space-y-0 p-4 pb-2 border-b">
                <div className="flex items-center gap-2">
                  <SheetTitle className="text-4xl font-black uppercase">
                    {env.APP_NAME}
                  </SheetTitle>

                  <div className="ml-auto flex items-center gap-2">
                    <ThemeToggle />
                    <LocaleSwitcher />
                    <Button
                      variant="ghost" size="icon"
                      onClick={() => setIsOpen(false)}
                      className="cursor-pointer h-8 w-8"
                    >
                      <X className="h-4 w-4" />
                    </Button>
                  </div>
                </div>
              </SheetHeader>

              <div className="flex-1 overflow-y-auto">
                <nav className="p-6 space-y-1">
                  {navigationItems.map((item) => (
                    <div key={item.name}>
                      {item.hasMegaMenu ? (
                        <Collapsible open={resourcesOpen} onOpenChange={setSolutionsOpen}>
                          <CollapsibleTrigger
                            className="flex items-center justify-between w-full px-4 py-3 text-base font-medium rounded-lg transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer">
                            {tc(item.name)}
                            <ChevronDown
                              className={`h-4 w-4 transition-transform ${resourcesOpen ? "rotate-180" : ""}`} />
                          </CollapsibleTrigger>
                          <CollapsibleContent className="pl-4 space-y-1">
                            {resourcesItems.map((resource, index) => (
                              resource.title ? (
                                <div
                                  key={`title-${index}`}
                                  className="px-4 mt-5 py-2 text-xs font-semibold text-muted-foreground/50 uppercase tracking-wider"
                                >
                                  {tc(resource.title)}
                                </div>
                              ) : (
                                <a
                                  key={resource.name}
                                  href={resource.href}
                                  className="flex items-center px-4 py-2 text-sm rounded-lg transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer"
                                  onClick={(e) => {
                                    setIsOpen(false)
                                    if (resource.href?.startsWith("#")) {
                                      e.preventDefault()
                                      setTimeout(() => smoothScrollTo(resource.href), 100)
                                    }
                                  }}
                                >
                                  {tc(resource.name!)}
                                </a>
                              )
                            ))}
                          </CollapsibleContent>
                        </Collapsible>
                      ) : (
                        <a
                          href={item.href}
                          className="flex items-center px-4 py-3 text-base font-medium rounded-lg transition-colors hover:bg-accent hover:text-accent-foreground cursor-pointer"
                          onClick={(e) => {
                            setIsOpen(false)
                            if (item.href.startsWith("#")) {
                              e.preventDefault()
                              setTimeout(() => smoothScrollTo(item.href), 100)
                            }
                          }}
                        >
                          {tc(item.name)}
                        </a>
                      )}
                    </div>
                  ))}
                </nav>
              </div>

              <div className="border-t p-6 space-y-4">
                <div className="space-y-3">
                  {!isLoading && (isAuthenticated ? (
                    <Button variant="outline" size="lg" asChild className="w-full cursor-pointer">
                      <Link href="/dashboard">
                        <LayoutDashboard className="size-4" />
                        Dashboard
                      </Link>
                    </Button>
                  ) : (
                    <div className="grid grid-cols-2 gap-3">
                      <Button variant="outline" size="lg" asChild className="cursor-pointer">
                        <Link href="/login">Sign In</Link>
                      </Button>
                      <Button asChild size="lg" className="cursor-pointer">
                        <Link href="/register">Get Started</Link>
                      </Button>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </SheetContent>
        </Sheet>
      </div>
    </header>
  )
}
