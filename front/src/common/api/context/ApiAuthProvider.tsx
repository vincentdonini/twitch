"use client";

import {
    createContext,
    useState,
    useEffect,
    ReactNode,
    useContext,
    useCallback
} from "react";
import {useDispatch} from "react-redux";
import {AppDispatch, fetchCurrentUser} from "@stores/stores";
import {AuthService} from "@common/api/application/AuthService";
import {LoginResponse} from "@common/api/infrastructure/ApiAuth";

interface AuthContextProps {
    // User
    isAuthenticated: boolean;
    isLoading: boolean;
    loginUser: (email: string, password: string) => Promise<void>;
    logoutUser: () => void;

    // Public
    isPublicAuthenticated: boolean;
    isPublicLoading: boolean;
    publicToken: string | null;
    loginPublic: () => Promise<LoginResponse>;
    logoutPublic: () => void;
    autoLoginPublic: () => Promise<boolean>;
}

function isTokenExpired(token: string): boolean {
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        const now = Math.floor(Date.now() / 1000); // timestamp en secondes
        return payload.exp < now;
    } catch {
        return true; // token invalide → considérer comme expiré
    }
}

export const ApiAuthContext = createContext<AuthContextProps | undefined>(undefined);

export const ApiAuthProvider = ({children}: { children: ReactNode }) => {
    const dispatch = useDispatch<AppDispatch>();

    // -------------------- USER SESSION --------------------
    const userAuthService = new AuthService("token", "refresh_token");

    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [isLoading, setIsLoading] = useState(true);

    const loginUser = async (email: string, password: string) => {
        setIsLoading(true);
        try {
            await userAuthService.login(email, password);
            await dispatch(fetchCurrentUser());
            setIsAuthenticated(true);
        } finally {
            setIsLoading(false);
        }
    };

    const logoutUser = async () => {
        setIsLoading(true);
        try {
            userAuthService.logout();
            setIsAuthenticated(false);
        } finally {
            setIsLoading(false);
        }
    };

    // -------------------- PUBLIC SESSION --------------------
    const publicAuthService = new AuthService("public_token", "public_refresh_token");

    const [publicToken, setPublicToken] = useState<string | null>(() => publicAuthService.getToken());
    const [isPublicAuthenticated, setIsPublicAuthenticated] = useState(!!publicToken);
    const [isPublicLoading, setIsPublicLoading] = useState(false);

    const loginPublic = async () => {
        setIsPublicLoading(true);
        try {
            const res = await publicAuthService.login(
                process.env.NEXT_PUBLIC_PUBLIC_EMAIL!,
                process.env.NEXT_PUBLIC_PUBLIC_PASSWORD!
            );
            setPublicToken(res.token);
            setIsPublicAuthenticated(true);
            return res;
        } catch (err) {
            console.error("Public login failed", err);
            setIsPublicAuthenticated(false);
            throw err;
        } finally {
            setIsPublicLoading(false);
        }
    };

    const logoutPublic = () => {
        publicAuthService.logout();
        setPublicToken(null);
        setIsPublicAuthenticated(false);
    };

    const autoLoginPublic = useCallback(async (): Promise<boolean> => {
        const token = publicAuthService.getToken();

        if (token && !isTokenExpired(token)) {
            return true; // token valide
        }

        const refreshToken = publicAuthService.getRefreshToken();
        if (refreshToken) {
            const newToken = await publicAuthService.refreshToken();
            if (newToken) {
                setPublicToken(newToken);
                setIsPublicAuthenticated(true);
                return true;
            } else {
                setPublicToken(null);
                setIsPublicAuthenticated(false);
                return false;
            }
        }

        // Pas de refresh token → login public
        try {
            const res = await loginPublic();
            setPublicToken(res.token);
            setIsPublicAuthenticated(true);
            return true;
        } catch {
            setPublicToken(null);
            setIsPublicAuthenticated(false);
            return false;
        }
    }, [publicAuthService, loginPublic]);

    useEffect(() => {
        const checkAuthPublic = async () => {
            setIsPublicLoading(true);
            try {
                const success = await autoLoginPublic();
                setIsPublicAuthenticated(success);
            } catch {
                setIsPublicAuthenticated(false);
                userAuthService.logout();
            } finally {
                setIsPublicLoading(false);
            }
        };
        checkAuthPublic();
    }, [autoLoginPublic]);

    return (
        <ApiAuthContext.Provider
            value={{
                // User
                isAuthenticated,
                isLoading,
                loginUser,
                logoutUser,

                // Public
                isPublicAuthenticated,
                isPublicLoading,
                publicToken,
                loginPublic,
                logoutPublic,
                autoLoginPublic,
            }}
        >
            {children}
        </ApiAuthContext.Provider>
    );
};

export const useApiAuth = () => {
    const context = useContext(ApiAuthContext);
    if (!context)
        throw new Error("useApiAuth must be used within an <ApiAuthProvider>");
    return context;
};
