import {MuscleArea} from "@common/api/domain/Muscle";
import {apiFetch} from "@common/api/infrastructure/client";
import {buildQueryString} from "@common/utils/queryString";
import {Pagination} from "@common/hooks/usePagination";

export async function fetchMuscleAreas(params: Record<string, unknown> = {}): Promise<{
    data: MuscleArea[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/muscle-areas?${queryString}` : "/muscle-areas";

    const {data, headers} = await apiFetch<MuscleArea[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("Pagination-Count") || 0),
        page: Number(headers.get("Pagination-Page") || 1),
        perPage: Number(headers.get("Pagination-Per-Page") || 10),
    };

    return {data, pagination};
}

export async function fetchMuscleAreaById(id: number): Promise<MuscleArea> {
    const {data} = await apiFetch<MuscleArea>(`/muscle-areas/${id}`, {}, {}, true, true);
    return data;
}
