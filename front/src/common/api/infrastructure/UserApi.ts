import {User} from "@common/api/domain/User";
import {apiFetch} from "@common/api/infrastructure/client";
import {Pagination} from "@common/hooks/usePagination";
import {buildQueryString} from "@common/utils/queryString";

export async function fetchUsers(params: Record<string, unknown> = {}): Promise<{
    data: User[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/users?${queryString}` : "/users";

    const {data, headers} = await apiFetch<User[]>(url);

    const pagination = {
        count: Number(headers.get("Pagination-Count") || 0),
        page: Number(headers.get("Pagination-Page") || 1),
        perPage: Number(headers.get("Pagination-Per-Page") || 10),
    };

    return {data, pagination};
}

export async function fetchUserById(id: number): Promise<User> {
    const {data} = await apiFetch<User>(`/users/${id}`);
    return data;
}

export async function fetchMe(): Promise<User> {
    const {data} = await apiFetch<User>('/users/me');
    return data;
}
