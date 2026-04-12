"use client";

import * as React from "react";

export type SidebarVariant = "sidebar" | "inset" | "floating";
export type SidebarCollapsible = "offcanvas" | "icon" | "none";
export type SidebarSide = "left" | "right";

export interface SidebarConfig {
    variant: SidebarVariant;
    collapsible: SidebarCollapsible;
    side: SidebarSide;
}

export interface SidebarContextValue {
    config: SidebarConfig;
    updateConfig: (config: Partial<SidebarConfig>) => void;
}

export const SidebarContext = React.createContext<SidebarContextValue | null>(null);

export function SidebarConfigProvider({ children }: { children: React.ReactNode }) {
    const [config, setConfig] = React.useState<SidebarConfig>({
        variant: "inset",
        collapsible: "offcanvas",
        side: "left",
    });

    const updateConfig = React.useCallback((newConfig: Partial<SidebarConfig>) => {
        setConfig((prev) => ({ ...prev, ...newConfig }));
    }, []);

    return (
        <SidebarContext.Provider value={{ config, updateConfig }}>
            {children}
        </SidebarContext.Provider>
    );
}

export function useSidebarConfig(): SidebarContextValue {
    const context = React.useContext(SidebarContext);
    if (!context) {
        throw new Error("useSidebarConfig must be used within a SidebarConfigProvider");
    }
    return context;
}
