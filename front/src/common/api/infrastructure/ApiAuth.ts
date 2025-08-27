import {apiFetch} from "@common/api/infrastructure/client";

export type LoginPayload = {
    email: string;
    password: string;
};

export async function login(payload: LoginPayload): Promise<{ token: string }> {
    return await apiFetch<{ token: string }>("/auth/login", {
        method: "POST",
        body: JSON.stringify(payload),
    }, {}, false);
}