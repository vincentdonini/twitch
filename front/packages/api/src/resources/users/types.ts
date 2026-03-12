export type User = {
    id: string;
    email: string;
    firstName: string;
    lastName: string;
    roles: string[];
};

export type UserMe = User & {
    permissions: string[];
};

export type CreateUserPayload = {
    email: string;
    firstName: string;
    lastName: string;
    password: string;
    roles?: string[];
};

export type UpdateUserPayload = Partial<Omit<CreateUserPayload, "password"> & { password?: string }>;
