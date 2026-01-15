"use client";

import {useEffect} from "react";
import {useRouter} from "next/navigation";
import {authenticateUser} from "@/modules/twitch/application/AuthenticateUser";

export default function Callback() {
    const router = useRouter();

    useEffect(() => {
        const fetchToken = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get("code");

            if (code) {
                try {
                    await authenticateUser(code);
                    router.push("/twitch/stream");
                } catch (error) {
                    console.error("Erreur d'authentification", error);
                }
            }
        };

        fetchToken();
    }, [router]);

    return (
        <p>Connexion en cours...</p>
    );
}
