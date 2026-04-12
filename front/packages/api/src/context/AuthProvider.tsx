"use client";

import {createContext, type ReactNode, useCallback, useContext, useEffect, useState} from "react";
import {login as apiLogin, logout as apiLogout} from "../auth";
import {clearTokens, getAccessToken, getRefreshToken, saveTokens} from "../storage";
import {apiFetch} from "../client";
import type {AuthTokens, LoginPayload} from "../types";
import type {UserMe} from "../resources/users/types";

interface AuthContextValue {
    isAuthenticated: boolean;
    isLoading: boolean;
    user: UserMe | null;
    loginUser: (email: string, password: string) => Promise<void>;
    logout: () => void;
    refreshMe: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

let loginPromise: Promise<void> | null = null;

export function AuthProvider({children}: { children: ReactNode }) {
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [isLoading, setIsLoading] = useState(true);
    const [user, setUser] = useState<UserMe | null>(null);

    const logout = useCallback(() => {
        apiLogout();
        setIsAuthenticated(false);
        setUser(null);
    }, []);

    const fetchMe = useCallback(async () => {
        try {
            const me = await apiFetch<UserMe>("/users/me", {method: "GET"}, {}, true);
            setUser(me);
        } catch {
            setUser(null);
        }
    }, []);

    const refreshSession = useCallback(async (): Promise<boolean> => {
        const refreshToken = getRefreshToken();
        if (!refreshToken) return false;

        try {
            const tokens = await apiFetch<AuthTokens>(
                "/auth/token/refresh",
                {method: "POST", body: JSON.stringify({refresh_token: refreshToken})},
                {},
                false
            );
            saveTokens(tokens.token, tokens.refresh_token);
            setIsAuthenticated(true);
            return true;
        } catch {
            clearTokens();
            setIsAuthenticated(false);
            return false;
        }
    }, []);

    useEffect(() => {
        const token = getAccessToken();
        if (token) {
            setIsAuthenticated(true);
            setIsLoading(false);
            fetchMe();
            return;
        }

        refreshSession().then((ok) => {
            if (ok) fetchMe();
        }).finally(() => setIsLoading(false));
    }, [refreshSession, fetchMe]);

    const loginUser = async (email: string, password: string): Promise<void> => {
        if (loginPromise) return loginPromise;

        setIsLoading(true);

        loginPromise = apiLogin({email, password} as LoginPayload)
            .then(async () => {
                setIsAuthenticated(true);
                await fetchMe();
            })
            .catch((err: unknown) => {
                setIsAuthenticated(false);
                throw err;
            })
            .finally(() => {
                setIsLoading(false);
                loginPromise = null;
            });

        return loginPromise;
    };

    return (
        <AuthContext.Provider value={{isAuthenticated, isLoading, user, loginUser, logout, refreshMe: fetchMe}}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth(): AuthContextValue {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within <AuthProvider>");
    }
    return context;
}
