"use client";

import React, {useEffect} from "react";
import {useRouter} from "next/navigation";
import {authenticateUser} from "@modules/twitch/application/AuthenticateUser";

export default function TwitchCallbackPage() {
    const router = useRouter();

    useEffect(() => {
        const fetchToken = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get("code");

            if (code) {
                try {
                    await authenticateUser(code);
                } catch (error) {
                    console.error("Twitch authentication error", error);
                }
            }
        };

        fetchToken()
            .then(() => {
                router.push("/twitch/stream");
            });
    }, [router]);

    return (
        <html lang="fr">
        <body>
            <p>TWITCH - Connecting...</p>
        </body>
        </html>
    );
};