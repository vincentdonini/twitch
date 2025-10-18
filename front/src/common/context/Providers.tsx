"use client";

import {ReactNode} from "react";
import {Provider} from "react-redux";
import {PersistGate} from "redux-persist/integration/react";
import {store, persistor} from "@stores/stores";
import {ApiAuthProvider} from "@common/api/context/ApiAuthProvider";
import {QueryClient, QueryClientProvider} from "@tanstack/react-query";

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            staleTime: 1000 * 60 * 5, // 5 minutes sans refetch
            gcTime: 1000 * 60 * 30,   // remplace cacheTime → 30 minutes avant suppression du cache
            retry: 1,
            refetchOnWindowFocus: false,
        },
    },
});

export default function Providers({children}: { children: ReactNode }) {
    return (
        <Provider store={store}>
            <PersistGate loading={null} persistor={persistor}>
                <ApiAuthProvider>
                    <QueryClientProvider client={queryClient}>
                        {children}
                    </QueryClientProvider>
                </ApiAuthProvider>
            </PersistGate>
        </Provider>
    );
}
