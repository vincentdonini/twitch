/**
 * Cookie-based token storage.
 * Using cookies with domain=.twitch.woder.local enables SSO across all subdomains
 * (front.twitch.woder.local, admin.twitch.woder.local, ...).
 */

const COOKIE_DOMAIN =
    typeof process !== "undefined"
        ? (process.env.NEXT_PUBLIC_COOKIE_DOMAIN ?? ".twitch.woder.local")
        : ".twitch.woder.local";

// Access token: short-lived, kept in memory + cookie
const ACCESS_TOKEN_KEY = "auth_token";
// Refresh token: longer-lived, cookie only
const REFRESH_TOKEN_KEY = "auth_refresh_token";

function setCookie(name: string, value: string, days = 1): void {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; domain=${COOKIE_DOMAIN}; SameSite=Lax`;
}

function getCookie(name: string): string | null {
    const match = document.cookie
        .split("; ")
        .find((row) => row.startsWith(`${name}=`));
    return match ? decodeURIComponent(match.split("=")[1] ?? "") : null;
}

function deleteCookie(name: string): void {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${COOKIE_DOMAIN}; SameSite=Lax`;
}

export function getAccessToken(): string | null {
    return getCookie(ACCESS_TOKEN_KEY);
}

export function getRefreshToken(): string | null {
    return getCookie(REFRESH_TOKEN_KEY);
}

export function saveTokens(token: string, refreshToken: string): void {
    setCookie(ACCESS_TOKEN_KEY, token, 1);          // 1 day
    setCookie(REFRESH_TOKEN_KEY, refreshToken, 30); // 30 days
}

export function clearTokens(): void {
    deleteCookie(ACCESS_TOKEN_KEY);
    deleteCookie(REFRESH_TOKEN_KEY);
}
