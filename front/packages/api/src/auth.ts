import {apiFetch} from "./client";
import {clearTokens, saveTokens} from "./storage";
import type {AuthTokens, LoginPayload, RegisterPayload} from "./types";

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

export async function register(payload: RegisterPayload): Promise<void> {
    await apiFetch<{ message: string }>(
        "/auth/register",
        {method: "POST", body: JSON.stringify(payload)},
        {},
        false
    );
}
