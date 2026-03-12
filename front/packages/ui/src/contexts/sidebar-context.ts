"use client";

import * as React from "react";

export type SidebarVariant = "default" | "inset" | "floating";
export type SidebarCollapsible = "offcanvas" | "icon" | "none";
export type SidebarSide = "left" | "right";

export interface SidebarConfig {
    variant: SidebarVariant;
    collapsible: SidebarCollapsible;
    side: SidebarSide;
}

export interface SidebarContextValue {
    config: SidebarConfig;
    setConfig: (config: Partial<SidebarConfig>) => void;
}

export const SidebarContext = React.createContext<SidebarContextValue | undefined>(undefined);
