"use client"

import { Separator } from "@workspace/ui/components/separator"
import { Button } from "@workspace/ui/components/button"
import { Logo } from "@workspace/ui/components/logo"
import { Heart, Linkedin, Twitter, Youtube } from "lucide-react"
import { env } from "@/lib/env"
import { useTranslations } from "next-intl"

const footerLinks = {
  product: [
    { name: "Features", href: "#features" },
    { name: "Pricing", href: "#pricing" },
    { name: "API", href: "#api" },
    { name: "Documentation", href: "#docs" },
  ],
  company: [
    { name: "About", href: "#about" },
    { name: "Blog", href: "#blog" },
    { name: "Careers", href: "#careers" },
    { name: "Press", href: "#press" },
  ],
  resources: [
    { name: "Help Center", href: "#help" },
    { name: "Community", href: "#community" },
    { name: "Guides", href: "#guides" },
    { name: "Webinars", href: "#webinars" },
  ],
  legal: [
    { name: "Privacy", href: "#privacy" },
    { name: "Terms", href: "#terms" },
    { name: "Security", href: "#security" },
    { name: "Status", href: "#status" },
  ],
}

const socialLinks = [
  { name: "Twitter", href: "#", icon: Twitter },
  { name: "LinkedIn", href: "#", icon: Linkedin },
  { name: "YouTube", href: "#", icon: Youtube },
]

export function Footer() {
  const tc = useTranslations("common")

  return (
    <footer className="border-t bg-background">
      <div className="container mx-auto px-4 py-8">
        {/* Newsletter Section */}
        {/*<div className="mb-16">*/}
        {/*  <div className="mx-auto max-w-2xl text-center">*/}
        {/*    <h3 className="text-2xl font-bold mb-4">Stay updated</h3>*/}
        {/*    <p className="text-muted-foreground mb-6">*/}
        {/*      Get the latest updates, articles, and resources sent to your inbox weekly.*/}
        {/*    </p>*/}
        {/*  </div>*/}
        {/*</div>*/}

        {/* Main Footer Content */}
        {/*<div className="grid gap-8 grid-cols-4 lg:grid-cols-6">*/}
        {/*  /!* Brand Column *!/*/}
        {/*  <div className="col-span-4 lg:col-span-2 max-w-2xl">*/}
        {/*    <div className="flex items-center space-x-2 mb-4 max-lg:justify-center">*/}
        {/*      <a href={env.APP_URL} target="_blank"*/}
        {/*         className="flex items-center space-x-2 cursor-pointer">*/}
        {/*        <Logo size={32} />*/}
        {/*        <span className="font-bold text-xl">*/}
        {/*          {env.APP_NAME}*/}
        {/*        </span>*/}
        {/*      </a>*/}
        {/*    </div>*/}
        {/*    <p className="text-muted-foreground mb-6 max-lg:text-center max-lg:flex max-lg:justify-center">*/}
        {/*      Accelerating web development with curated blocks, templates, landing pages, and admin*/}
        {/*      dashboards designed for modern developers.*/}
        {/*    </p>*/}
        {/*    <div className="flex space-x-4 max-lg:justify-center">*/}
        {/*      {socialLinks.map((social) => (*/}
        {/*        <Button key={social.name} variant="ghost" size="icon" asChild>*/}
        {/*          <a*/}
        {/*            href={social.href}*/}
        {/*            aria-label={social.name}*/}
        {/*            target="_blank"*/}
        {/*            rel="noopener noreferrer"*/}
        {/*          >*/}
        {/*            <social.icon className="h-4 w-4" />*/}
        {/*          </a>*/}
        {/*        </Button>*/}
        {/*      ))}*/}
        {/*    </div>*/}
        {/*  </div>*/}

        {/*  /!* Links Columns *!/*/}
        {/*  <div className="max-md:col-span-2 lg:col-span-1">*/}
        {/*    <h4 className="font-semibold mb-4">Product</h4>*/}
        {/*    <ul className="space-y-3">*/}
        {/*      {footerLinks.product.map((link) => (*/}
        {/*        <li key={link.name}>*/}
        {/*          <a*/}
        {/*            href={link.href}*/}
        {/*            className="text-muted-foreground hover:text-foreground transition-colors"*/}
        {/*          >*/}
        {/*            {link.name}*/}
        {/*          </a>*/}
        {/*        </li>*/}
        {/*      ))}*/}
        {/*    </ul>*/}
        {/*  </div>*/}

        {/*  <div className="max-md:col-span-2 lg:col-span-1">*/}
        {/*    <h4 className="font-semibold mb-4">Company</h4>*/}
        {/*    <ul className="space-y-3">*/}
        {/*      {footerLinks.company.map((link) => (*/}
        {/*        <li key={link.name}>*/}
        {/*          <a*/}
        {/*            href={link.href}*/}
        {/*            className="text-muted-foreground hover:text-foreground transition-colors"*/}
        {/*          >*/}
        {/*            {link.name}*/}
        {/*          </a>*/}
        {/*        </li>*/}
        {/*      ))}*/}
        {/*    </ul>*/}
        {/*  </div>*/}

        {/*  <div className="max-md:col-span-2 lg:col-span-1">*/}
        {/*    <h4 className="font-semibold mb-4">Resources</h4>*/}
        {/*    <ul className="space-y-3">*/}
        {/*      {footerLinks.resources.map((link) => (*/}
        {/*        <li key={link.name}>*/}
        {/*          <a*/}
        {/*            href={link.href}*/}
        {/*            className="text-muted-foreground hover:text-foreground transition-colors"*/}
        {/*          >*/}
        {/*            {link.name}*/}
        {/*          </a>*/}
        {/*        </li>*/}
        {/*      ))}*/}
        {/*    </ul>*/}
        {/*  </div>*/}

        {/*  <div className="max-md:col-span-2 lg:col-span-1">*/}
        {/*    <h4 className="font-semibold mb-4">Legal</h4>*/}
        {/*    <ul className="space-y-3">*/}
        {/*      {footerLinks.legal.map((link) => (*/}
        {/*        <li key={link.name}>*/}
        {/*          <a*/}
        {/*            href={link.href}*/}
        {/*            className="text-muted-foreground hover:text-foreground transition-colors"*/}
        {/*          >*/}
        {/*            {link.name}*/}
        {/*          </a>*/}
        {/*        </li>*/}
        {/*      ))}*/}
        {/*    </ul>*/}
        {/*  </div>*/}
        {/*</div>*/}

        {/*<Separator className="my-8" />*/}

        <div className="flex flex-col items-center md:items-start">
          <div className="flex items-center text-foreground gap-1 font-semibold">
            <span>© {new Date().getFullYear()}</span>
            <div>
              <a className="font-semibold hover:text-primary" href={env.APP_URL} title={env.APP_NAME}>{env.APP_NAME}</a>.
            </div>
            <span>{tc("all_rights_reserved")}</span>
          </div>
          <div className="hidden md:block flex flex-col md:flex-row items-center space-x-4 text-sm text-muted-foreground mt-4 md:mt-0">
            <a href="/privacy" className="text-center hover:text-foreground transition-colors">
              {tc("privacy_policy")}
            </a>
            <span className="hidden md:inline">•</span>
            <a href="/terms" className="text-center hover:text-foreground transition-colors">
              {tc("term_of_service")}
            </a>
            <span className="hidden md:inline">•</span>
            <a href="/cookies" className="text-center hover:text-foreground transition-colors">
              {tc("cookie_policy")}
            </a>
          </div>
        </div>
      </div>
    </footer>
  )
}
