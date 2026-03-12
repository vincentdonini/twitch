"use client";

import {useQuery} from "../../hooks/useQuery";
import type {User} from "./types";

export function useGetUser(id: string) {
    return useQuery<User>(`/users/${id}`);
}
