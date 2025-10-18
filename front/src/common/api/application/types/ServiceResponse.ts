import {Pagination} from "@common/hooks/usePagination";

export interface ServiceResponse<T> {
    data: T;
    pagination?: Pagination;
    error?: string | null;
    success: boolean;
}