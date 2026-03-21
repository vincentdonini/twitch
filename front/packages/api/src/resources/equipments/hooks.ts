"use client";

import { useQuery } from "../../hooks/useQuery";
import type { Equipment, EquipmentSummary } from "./types";

export function useGetEquipments(params: Record<string, string> = {}) {
    return useQuery<EquipmentSummary[]>("/equipments", params);
}

export function useGetEquipment(id: string) {
    return useQuery<Equipment>(`/equipments/${id}`);
}
