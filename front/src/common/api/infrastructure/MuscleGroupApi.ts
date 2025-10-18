import {MuscleGroup} from "@common/api/domain/Muscle";
import {apiFetch} from "@common/api/infrastructure/client";
import {buildQueryString} from "@common/utils/queryString";
import {Pagination} from "@common/hooks/usePagination";

export async function fetchMuscleGroups(params: Record<string, unknown> = {}): Promise<{
    data: MuscleGroup[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/muscle-groups?${queryString}` : "/muscle-groups";

    const {data, headers} = await apiFetch<MuscleGroup[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("Pagination-Count") || 0),
        page: Number(headers.get("Pagination-Page") || 1),
        perPage: Number(headers.get("Pagination-Per-Page") || 10),
    };

    return {data, pagination};
}

export async function fetchMuscleGroupById(id: number): Promise<MuscleGroup> {
    const {data} = await apiFetch<MuscleGroup>(`/muscle-groups/${id}`, {}, {}, true, true);
    return data;
}

