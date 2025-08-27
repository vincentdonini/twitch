import {createContext, useState, useEffect, ReactNode, useContext} from "react";
import {SpotifyAuth} from "@modules/spotify/infrastructure/SpotifyAuth";

interface AuthContextProps {
    token: string | null;
    login: () => void;
    logout: () => void;
    loading: boolean;
}

export const SpotifyAuthContext = createContext<AuthContextProps | undefined>(undefined);

export const SpotifyAuthProvider = ({children}: { children: ReactNode }) => {
    const [token, setToken] = useState<string | null>(null);
    const [loading, setLoading] = useState<boolean>(true);

    useEffect(() => {
        const storedToken = localStorage.getItem("spotify_token");
        const storedTokenExpiry = localStorage.getItem("spotify_token_expiry");
        const storedRefreshToken = localStorage.getItem("spotify_refresh_token");

        if (storedToken && storedTokenExpiry && storedRefreshToken) {
            setToken(storedToken);
            setLoading(true);
        } else {
            login();
        }

        setLoading(false);
    }, []);

    const login = () => {
        window.location.href = SpotifyAuth.getLoginUrl();
    };

    const logout = () => {
        setToken(null);
        localStorage.removeItem("spotify_token");
        localStorage.removeItem("spotify_token_expiry");
        localStorage.removeItem("spotify_refresh_token");
    };

    return (
        <SpotifyAuthContext.Provider value={{token, loading, login, logout}}>
            {children}
        </SpotifyAuthContext.Provider>
    );
};

export const useSpotifyAuth = () => {
    const context = useContext(SpotifyAuthContext);
    if (!context) {
        throw new Error('useAuth must be used inside <SpotifyAuthContext>');
    }
    return context;
};