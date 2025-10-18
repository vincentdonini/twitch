const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL;

// Récupère la locale courante
function getCurrentLocale(): string {
    // 1. Si tu stockes la locale côté front
    const storedLocale = typeof window !== "undefined" ? localStorage.getItem("locale") : null;
    if (storedLocale) return storedLocale;

    // 2. Sinon on prend celle du navigateur
    if (typeof navigator !== "undefined" && navigator.language) {
        return navigator.language.split("-")[0]; // ex: "fr" à partir de "fr-FR"
    }

    // 3. Fallback
    return "en";
}

export interface ApiResponse<T> {
    data: T;
    headers: Headers;
}

export async function apiFetch<T>(
    url: string,
    options: RequestInit = {},
    params: Record<string, string> = {},
    auth: boolean = true,
    usePublicToken: boolean = false
): Promise<ApiResponse<T>> {
    let token: string | null = null;
    if (typeof window !== "undefined") {
        token = usePublicToken
            ? localStorage.getItem("public_token")
            : localStorage.getItem("token");
    }

    const locale = getCurrentLocale();

    const queryParams = new URLSearchParams(params).toString();
    const localizedUrl = auth ? `/${locale}${url}` : url;
    const fullUrl = queryParams
        ? `${API_BASE_URL}${localizedUrl}?${queryParams}`
        : `${API_BASE_URL}${localizedUrl}`;

    const headers: Record<string, string> = {
        "Content-Type": "application/json",
        ...(options.headers as Record<string, string>),
    };

    if (auth && token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const res = await fetch(fullUrl, {
        ...options,
        headers,
    });

    if (!res.ok) {
        const errorText = await res.text();
        throw new Error(errorText || "Erreur serveur");
    }

    const data: T = await res.json();

    return {
        data,
        headers: res.headers,
    };
}
