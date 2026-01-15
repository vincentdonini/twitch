"use client";

import React, {useEffect} from "react";
import {useRouter} from "next/navigation";
import {authenticateUser} from "@modules/spotify/application/AuthenticateUser";

export default function SpotifyCallbackPage() {
    const router = useRouter();

    useEffect(() => {
        const fetchToken = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get("code");

            if (code) {
                try {
                    await authenticateUser(code);
                } catch (error) {
                    console.error("Spotify authentication error", error);
                }
            }
        };

        fetchToken()
            .then(() => router.push("/twitch/stream"));
    }, [router]);

    return (
        <html lang="fr">
        <body>
            <p>SPOTIFY - Connecting...</p>
        </body>
        </html>
    );
};