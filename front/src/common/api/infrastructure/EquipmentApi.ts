import {Equipment} from "@common/api/domain/Equipment";
import {apiFetch, ApiResponse} from "@common/api/infrastructure/client";
import {Pagination} from "@common/hooks/usePagination";

function buildQueryString(params: Record<string, unknown>): string {
    const qs = new URLSearchParams();

    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
            qs.append(key, value.toString());
        }
    });

    return qs.toString();
}

export async function fetchEquipments(params: Record<string, unknown> = {}): Promise<{
    data: Equipment[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/equipments?${queryString}` : "/equipments";

    const {data, headers}: ApiResponse<Equipment[]> = await apiFetch<Equipment[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("Pagination-Count") || 0),
        page: Number(headers.get("Pagination-Page") || 1),
        perPage: Number(headers.get("Pagination-Per-Page") || 10),
    };

    return {data, pagination};
}

export async function fetchEquipmentById(id: number): Promise<Equipment> {
    const {data}: ApiResponse<Equipment> = await apiFetch<Equipment>(`/equipments/${id}`, {}, {}, true, true);
    return data;
}