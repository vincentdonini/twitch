import {Muscle} from "@common/api/domain/Muscle";
import {apiFetch} from "@common/api/infrastructure/client";
import {buildQueryString} from "@common/utils/queryString";
import {Pagination} from "@common/hooks/usePagination";

export async function fetchMuscles(params: Record<string, unknown> = {}): Promise<{
    data: Muscle[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/muscles?${queryString}` : "/muscles";

    const {data, headers} = await apiFetch<Muscle[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("Pagination-Count") || 0),
        page: Number(headers.get("Pagination-Page") || 1),
        perPage: Number(headers.get("Pagination-Per-Page") || 10),
    };

    return {data, pagination};
}

export async function fetchMuscleById(id: number): Promise<Muscle> {
    const {data} = await apiFetch<Muscle>(`/muscles/${id}`, {}, {}, true, true);
    return data;
}