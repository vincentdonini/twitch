import {clearTokens, getAccessToken, getRefreshToken, saveTokens} from "./storage";
import type {AuthTokens} from "./types";

const API_BASE_URL =
    typeof process !== "undefined"
        ? (process.env.NEXT_PUBLIC_API_URL ?? "")
        : "";

const API_PUBLIC_KEY =
    typeof process !== "undefined"
        ? (process.env.NEXT_PUBLIC_API_PUBLIC_KEY ?? "")
        : "";

const AUTH_PATHS = ["/auth/", "/token/"];

function getLocale(): string {
    if (typeof document !== "undefined") {
        const match = document.cookie.match(/(?:^|;\s*)locale=([^;]*)/);
        if (match && match[1]) return match[1];
    }

    return process.env.NEXT_PUBLIC_API_LOCALE ?? "";
}

function withLocale(url: string): string {
    const locale = getLocale();
    if (!locale) return url;
    if (AUTH_PATHS.some((prefix) => url.startsWith(prefix))) return url;
    if (url.startsWith(`/${locale}/`)) return url;
    return `/${locale}${url}`;
}

let isRefreshing = false;
let refreshQueue: Array<(success: boolean) => void> = [];

async function doRefresh(): Promise<boolean> {
    const refreshToken = getRefreshToken();
    if (!refreshToken) return false;

    try {
        const res = await fetch(`${API_BASE_URL}/auth/token/refresh`, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({refresh_token: refreshToken}),
        });

        if (!res.ok) {
            clearTokens();
            return false;
        }

        const tokens: AuthTokens = await res.json();
        saveTokens(tokens.token, tokens.refresh_token);
        return true;
    } catch {
        clearTokens();
        return false;
    }
}

async function tryRefresh(): Promise<boolean> {
    if (isRefreshing) {
        return new Promise((resolve) => {
            refreshQueue.push(resolve);
        });
    }

    isRefreshing = true;
    const success = await doRefresh();
    isRefreshing = false;

    refreshQueue.forEach((cb) => cb(success));
    refreshQueue = [];

    return success;
}

export type PaginationMeta = {
    total: number;
    page: number;
    totalPages: number;
    limit: number;
};

function parsePagination(headers: Headers): PaginationMeta | null {
    const total = headers.get("Element-Count");
    const page = headers.get("Pagination-Page");
    const totalPages = headers.get("Pagination-Count");
    const limit = headers.get("Pagination-Limit");
    if (!total || !page || !totalPages || !limit) return null;
    return {
        total: Number(total),
        page: Number(page),
        totalPages: Number(totalPages),
        limit: Number(limit),
    };
}

type AuthMethod = "jwt" | "apikey" | "none";

function buildQueryString(params: Record<string, string | string[]>): string {
    const parts: string[] = [];
    for (const [key, value] of Object.entries(params)) {
        if (Array.isArray(value)) {
            for (const v of value) {
                parts.push(`${encodeURIComponent(key)}[]=${encodeURIComponent(v)}`);
            }
        } else {
            parts.push(`${encodeURIComponent(key)}=${encodeURIComponent(value)}`);
        }
    }
    return parts.join("&");
}

async function executeRequest(
    url: string,
    options: RequestInit = {},
    params: Record<string, string | string[]> = {},
    auth = true
): Promise<Response> {
    const isAuthPath = AUTH_PATHS.some((prefix) => url.startsWith(prefix));
    const token = auth ? getAccessToken() : null;

    const localizedUrl = withLocale(url);
    const queryString = buildQueryString(params);
    const fullUrl = queryString
        ? `${API_BASE_URL}${localizedUrl}?${queryString}`
        : `${API_BASE_URL}${localizedUrl}`;

    const headers: Record<string, string> = {
        "Content-Type": "application/json",
        ...(options.headers as Record<string, string>),
    };

    // Auth strategy:
    // - auth endpoints (/auth/, /token/) → no header
    // - user has JWT → Bearer token
    // - no JWT but public key available → X-API-Key (anonymous public access)
    let authMethod: AuthMethod = "none";

    if (auth && !isAuthPath) {
        if (token) {
            headers.Authorization = `Bearer ${token}`;
            authMethod = "jwt";
        } else if (API_PUBLIC_KEY) {
            headers["X-API-Key"] = API_PUBLIC_KEY;
            authMethod = "apikey";
        }
    }

    let res = await fetch(fullUrl, {...options, headers});

    // On 401 with JWT: try to refresh session
    // On 401 with API key: do not retry (the key is wrong or server issue)
    if (res.status === 401 && authMethod === "jwt") {
        const refreshed = await tryRefresh();
        if (refreshed) {
            const newToken = getAccessToken();
            if (newToken) headers.Authorization = `Bearer ${newToken}`;
            res = await fetch(fullUrl, {...options, headers});
        } else {
            throw new Error("Session expired");
        }
    }

    if (!res.ok) {
        const error = await res.text();
        throw new Error(error || `HTTP ${res.status}`);
    }

    return res;
}

export async function apiFetch<T>(
    url: string,
    options: RequestInit = {},
    params: Record<string, string | string[]> = {},
    auth = true
): Promise<T> {
    const res = await executeRequest(url, options, params, auth);
    if (res.status === 204 || res.headers.get("content-length") === "0") return null as T;
    return res.json() as Promise<T>;
}

export async function apiFetchWithMeta<T>(
    url: string,
    params: Record<string, string | string[]> = {},
    auth = true
): Promise<{ data: T; pagination: PaginationMeta | null }> {
    const res = await executeRequest(url, {method: "GET"}, params, auth);
    const data = (await res.json()) as T;
    return {data, pagination: parsePagination(res.headers)};
}
