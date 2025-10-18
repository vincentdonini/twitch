import {Wod, WodCategory, WodType, WodVersionType} from "@common/api/domain/Wod";
import {apiFetch} from "@common/api/infrastructure/client";
import {buildQueryString} from "@common/utils/queryString";
import {Pagination} from "@common/hooks/usePagination";

export async function fetchWods(params: Record<string, unknown> = {}): Promise<{
    data: Wod[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/wods?${queryString}` : "/wods";

    const {data, headers} = await apiFetch<Wod[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("pagination-count") || 0),
        page: Number(headers.get("pagination-page") || 1),
        perPage: Number(headers.get("pagination-per-page") || 10),
    };

    return {data, pagination};
}

export async function fetchWodById(id: number): Promise<Wod> {
    const {data} = await apiFetch<Wod>(`/wods/${id}`, {}, {}, true, true);
    return data;
}

export async function fetchWodCategories(params: Record<string, unknown> = {}): Promise<{
    data: WodCategory[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/wod-categories?${queryString}` : "/wod-categories";

    const {data, headers} = await apiFetch<WodCategory[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("pagination-count") || 0),
        page: Number(headers.get("pagination-page") || 1),
        perPage: Number(headers.get("pagination-per-page") || 10),
    };

    return {data, pagination};
}

export async function fetchWodCategoryById(id: number): Promise<WodCategory> {
    const {data} = await apiFetch<WodCategory>(`/wod-categories/${id}`, {}, {}, true, true);
    return data;
}

export async function fetchWodTypes(params: Record<string, unknown> = {}): Promise<{
    data: WodType[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/wod-types?${queryString}` : "/wod-types";

    const {data, headers} = await apiFetch<WodType[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("pagination-count") || 0),
        page: Number(headers.get("pagination-page") || 1),
        perPage: Number(headers.get("pagination-per-page") || 10),
    };

    return {data, pagination};
}

export async function fetchWodTypeById(id: number): Promise<WodType> {
    const {data} = await apiFetch<WodType>(`/wod-types/${id}`, {}, {}, true, true);
    return data;
}

export async function fetchWodVersionTypes(params: Record<string, unknown> = {}): Promise<{
    data: WodVersionType[];
    pagination: Pagination
}> {
    const queryString = buildQueryString(params);
    const url = queryString ? `/wod-version-types?${queryString}` : "/wod-version-types";

    const {data, headers} = await apiFetch<WodVersionType[]>(url, {}, {}, true, true);

    const pagination = {
        count: Number(headers.get("pagination-count") || 0),
        page: Number(headers.get("pagination-page") || 1),
        perPage: Number(headers.get("pagination-per-page") || 10),
    };

    return {data, pagination};
}

export async function fetchWodVersionTypeById(id: number): Promise<WodVersionType> {
    const {data} = await apiFetch<WodVersionType>(`/wod-version-types/${id}`, {}, {}, true, true);
    return data;
}
