import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {Pagination} from "@common/hooks/usePagination";

export abstract class BaseService {
    protected handleSuccess<T>(data: T, pagination?: Pagination): ServiceResponse<T> {
        return {data, pagination, success: true, error: null};
    }

    protected handleError<T>(error: unknown): ServiceResponse<T> {
        return {data: {} as T, success: false, error: (error as Error)?.message ?? "Unknown error"};
    }
}
