import {apiFetch} from "@common/api/infrastructure/client";
import {AppDispatch, fetchCurrentUser} from "@stores/stores";

interface LoginResponse {
    token: string;
    refresh_token?: string;
}

export async function login(
    email: string,
    password: string,
    dispatch: AppDispatch
): Promise<LoginResponse> {
    const {data} = await apiFetch<LoginResponse>("/auth/login", {
        method: "POST",
        body: JSON.stringify({email, password}),
    }, {}, false);

    localStorage.setItem("token", data.token);
    if (data.refresh_token) {
        localStorage.setItem("refresh_token", data.refresh_token);
    }

    await dispatch(fetchCurrentUser());

    return data;
}

export function logout() {
    localStorage.removeItem("token");
    localStorage.removeItem("refresh_token");
}