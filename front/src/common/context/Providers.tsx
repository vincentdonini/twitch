import {ReactNode} from "react";
import {Provider} from "react-redux";
import {store, persistor} from "@stores/stores";
import {PersistGate} from "redux-persist/integration/react";
import {ApiAuthProvider} from "@common/api/context/ApiAuthProvider";
import {SpotifyAuthProvider} from "@modules/spotify/context/SpotifyAuthProvider";
import {TwitchAuthProvider} from "@modules/twitch/context/TwitchAuthProvider";

export default function Providers({children}: { children: ReactNode }) {
    return (
        <Provider store={store}>
            <PersistGate loading={null} persistor={persistor}>
                <ApiAuthProvider>
                    <TwitchAuthProvider>
                        <SpotifyAuthProvider>
                            {children}
                        </SpotifyAuthProvider>
                    </TwitchAuthProvider>
                </ApiAuthProvider>
            </PersistGate>
        </Provider>
    );
}
