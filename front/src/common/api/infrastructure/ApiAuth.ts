import {apiFetch} from "@common/api/infrastructure/client";

export interface LoginResponse {
    token: string;
    refresh_token?: string;
}

export async function loginApi(email: string, password: string): Promise<LoginResponse> {
    const {data} = await apiFetch<LoginResponse>(
        "/auth/login",
        {
            method: "POST",
            body: JSON.stringify({email, password}),
        },
        {},
        false
    );
    return data;
}

export async function refreshTokenApi(refreshToken: string): Promise<LoginResponse> {
    const {data} = await apiFetch<LoginResponse>(
        "/auth/refresh-token",
        {
            method: "POST",
            body: JSON.stringify({}),
            headers: {
                Authorization: `Bearer ${refreshToken}`,
            },
        },
        {},
        false
    );
    return data;
}
