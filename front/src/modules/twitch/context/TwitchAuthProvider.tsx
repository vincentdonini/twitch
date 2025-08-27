import {createContext, useState, useEffect, ReactNode, useContext} from "react";
import {TwitchAuth} from "@modules/twitch/infrastructure/TwitchAuth";

interface AuthContextProps {
    token: string | null;
    login: () => void;
    logout: () => void;
    loading: boolean;
}

export const TwitchAuthContext = createContext<AuthContextProps | undefined>(undefined);

export const TwitchAuthProvider = ({children}: { children: ReactNode }) => {
    const [token, setToken] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const storedToken = localStorage.getItem("twitch_token");
        const storedTokenExpiry = localStorage.getItem("twitch_token_expiry");
        const storedRefreshToken = localStorage.getItem("twitch_refresh_token");

        if (storedToken && storedTokenExpiry && storedRefreshToken) {
            setToken(storedToken);
            setLoading(true);
        } else {
            login();
        }

        setLoading(false);
    }, []);

    const login = () => {
        window.location.href = TwitchAuth.getLoginUrl();
    };

    const logout = () => {
        setToken(null);
        localStorage.removeItem("twitch_token");
        localStorage.removeItem("twitch_token_expiry");
        localStorage.removeItem("twitch_refresh_token");
        localStorage.removeItem("twitch_scope");
    };

    return (
        <TwitchAuthContext.Provider value={{token, loading, login, logout}}>
            {children}
        </TwitchAuthContext.Provider>
    );
};

export const useTwitchAuth = () => {
    const context = useContext(TwitchAuthContext);
    if (!context) {
        throw new Error('useAuth must be used inside <TwitchAuthContext>');
    }
    return context;
};