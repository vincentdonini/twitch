import { useContext } from "react";
import { TwitchAuthContext } from "@/modules/twitch/context/TwitchAuthProvider";

export default function TwitchAuthButton() {
    const auth = useContext(TwitchAuthContext);
    if (!auth) return null;

    return (
        <div>
            {auth.user ? (
                <div>
                    <p className="pb-2">Bienvenue, {auth.user.name} {auth.user.id} {auth.token} !</p>
                    <button onClick={auth.logout} className="px-4 py-2 bg-red-600 text-white rounded">
                        Déconnexion
                    </button>
                </div>
            ) : (
                <button onClick={auth.login} className="px-4 py-2 bg-purple-600 text-white rounded">
                    Connexion Twitch
                </button>
            )}
        </div>
    );
}
