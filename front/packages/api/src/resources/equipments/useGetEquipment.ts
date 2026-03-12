"use client";

import {useQuery} from "../../hooks/useQuery";
import type {Equipment} from "./types";

export function useGetEquipment(id: string) {
    return useQuery<Equipment>(`/equipments/${id}`);
}
