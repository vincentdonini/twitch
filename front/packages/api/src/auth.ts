import {apiFetch} from "./client";
import {clearTokens, saveTokens} from "./storage";
import type {AuthTokens, LoginPayload} from "./types";

export async function login(payload: LoginPayload): Promise<AuthTokens> {
    const tokens = await apiFetch<AuthTokens>(
        "/auth/login",
        {method: "POST", body: JSON.stringify(payload)},
        {},
        false
    );
    saveTokens(tokens.token, tokens.refresh_token);
    return tokens;
}

export async function logout(): Promise<void> {
    clearTokens();
}
