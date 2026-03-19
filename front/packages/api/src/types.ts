export type LoginPayload = {
    email: string;
    password: string;
};

export type RegisterPayload = {
    email: string;
    password: string;
    firstName: string;
    lastName: string;
};

export type AuthTokens = {
    token: string;
    refresh_token: string;
};
