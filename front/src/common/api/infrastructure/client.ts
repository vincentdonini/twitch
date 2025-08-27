const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL;

import {tryAutoLogin} from "@common/api/infrastructure/authService";

export async function apiFetch<T>(
    url: string,
    options: RequestInit = {},
    params: Record<string, string> = {},
    auth: boolean = true
): Promise<T> {
    const token = localStorage.getItem('token');
    const queryParams = new URLSearchParams(params).toString();
    const fullUrl = queryParams ? `${API_BASE_URL}${url}?${queryParams}` : `${API_BASE_URL}${url}`;

    const headers: Record<string, string> = {
        "Content-Type": "application/json",
        ...(options.headers as Record<string, string>),
    };

    if (auth && token) {
        headers.Authorization = `Bearer ${token}`;
    }

    let res = await fetch(fullUrl, {
        ...options,
        headers,
    });

    if (res.status === 401 && auth) {
        console.warn("🔐 Token expiré, tentative de reconnexion...");

        localStorage.removeItem("token");

        const success = await tryAutoLogin();
        if (success) {
            const retryToken = localStorage.getItem("token");
            if (retryToken) {
                headers.Authorization = `Bearer ${retryToken}`;
            }

            res = await fetch(fullUrl, {
                ...options,
                headers,
            });
        } else {
            throw new Error("Reconnexion échouée");
        }
    }

    if (!res.ok) {
        const error = await res.text();
        throw new Error(error || "Erreur serveur");
    }

    return res.json();
}
