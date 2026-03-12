export type LoginPayload = {
    email: string;
    password: string;
};

export type AuthTokens = {
    token: string;
    refresh_token: string;
};
