"use client";

import {useQuery} from "../../hooks/useQuery";
import type {Equipment} from "./types";

export function useGetEquipments(params: Record<string, string> = {}) {
    return useQuery<Equipment[]>("/equipments", params);
}
