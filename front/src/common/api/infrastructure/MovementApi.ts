import {Movement} from "@common/api/domain/Movement";
import {apiFetch} from "@common/api/infrastructure/client";
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

export async function fetchMovements(params: Record<string, unknown> = {}): Promise<{
    data: Movement[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/movements?${queryString}` : "/movements";

    const {data, headers} = await apiFetch<Movement[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("pagination-count") || 0),
        page: Number(headers.get("pagination-page") || 1),
        perPage: Number(headers.get("pagination-per-page") || 10),
    };

    return {data, pagination};
}

export async function fetchMovementById(id: number): Promise<Movement> {
    const {data} = await apiFetch<Movement>(`/movements/${id}`, {}, {}, true, true);
    return data;
}

