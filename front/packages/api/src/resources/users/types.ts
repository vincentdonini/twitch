export type User = {
    id: string;
    email: string;
    firstName: string;
    lastName: string;
    roles: string[];
};

export type UserMeGymSubscription = {
    subscriptionId: string;
    placeId: string;
    placeName: string;
};

export type UserMePlace = {
    id: string;
    name: string;
};

export type UserMeCompany = {
    id: string;
    name: string;
    places: UserMePlace[];
};

export type UserMe = User & {
    permissions: string[];
    gymSubscriptions: UserMeGymSubscription[];
    coachPlaces: UserMePlace[];
    ownerCompanies: UserMeCompany[];
};

export type CreateUserPayload = {
    email: string;
    firstName: string;
    lastName: string;
    password: string;
    roles?: string[];
};

export type UpdateUserPayload = Partial<Omit<CreateUserPayload, "password"> & { password?: string }>;

export type UpdateMePayload = {
    email?: string
    firstName?: string
    lastName?: string
}

export type UpdatePasswordPayload = {
    currentPassword: string
    newPassword: string
}
