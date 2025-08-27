import {
    createContext,
    useState,
    useEffect,
    ReactNode,
    useContext,
    useCallback
} from "react";
import {login} from "@common/api/infrastructure/ApiAuth";
import {Api} from "@common/api/infrastructure/Api";
import {setActiveSubscriptions} from "@stores/activeSubscriptionsSlice";
import {store} from "@stores/stores";
import {setAutoLoginCallback} from "@common/api/infrastructure/authService";

interface AuthContextProps {
    isAuthenticated: boolean;
    isLoading: boolean;
    loginUser: (email: string, password: string) => Promise<void>;
    autoLogin: () => Promise<boolean>;
}

export const ApiAuthContext = createContext<AuthContextProps | undefined>(undefined);

let loginPromise: Promise<void> | null = null;

export const ApiAuthProvider = ({children}: { children: ReactNode }) => {
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [isLoading, setIsLoading] = useState(true);

    const autoLogin = useCallback(async (): Promise<boolean> => {
        const storedToken = localStorage.getItem("token");

        if (storedToken) {
            setIsAuthenticated(true);
            return true;
        }

        const email = process.env.NEXT_PUBLIC_API_ADMIN_USER ?? "";
        const password = process.env.NEXT_PUBLIC_API_ADMIN_PWD ?? "";

        if (email && password) {
            try {
                await loginUser(email, password);
                return true;
            } catch (err) {
                console.error("❌ Auto-login failed:", err);
            }
        }

        return false;
    }, []);

    useEffect(() => {
        setAutoLoginCallback(autoLogin);

        autoLogin()
            .then((success) => {
                if (success) {
                    console.log("✅ Auto-login successful");
                } else {
                    console.log("ℹ️ No auto-login attempted");
                }
                setIsLoading(false);
            });
    }, [autoLogin]);

    const loginUser = async (email: string, password: string): Promise<void> => {
        if (loginPromise) return loginPromise;

        setIsLoading(true);

        loginPromise = login({email, password})
            .then(({token}) => {
                localStorage.setItem("token", token);
                setIsAuthenticated(true);
                fetchAndSetActiveSubscriptions();
            })
            .catch((err) => {
                setIsAuthenticated(false);
                throw err;
            })
            .finally(() => {
                setIsLoading(false);
                loginPromise = null;
            });

        return loginPromise;
    };

    const fetchAndSetActiveSubscriptions = async () => {
        try {
            const activeSubscriptions = await Api.getActiveSubscriptions();
            const usernames = activeSubscriptions.map(sub => sub.username.toLowerCase());
            store.dispatch(setActiveSubscriptions(usernames));
        } catch (err) {
            console.error("Erreur lors de la récupération des abonnements actifs:", err);
        }
    };

    return (
        <ApiAuthContext.Provider value={{isAuthenticated, isLoading, loginUser, autoLogin}}>
            {children}
        </ApiAuthContext.Provider>
    );
};

export const useApiAuth = () => {
    const context = useContext(ApiAuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an <ApiAuthProvider>');
    }
    return context;
};
