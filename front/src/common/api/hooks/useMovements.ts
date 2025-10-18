import {useInfiniteQuery, InfiniteData} from "@tanstack/react-query";
import {MovementService} from "@common/api/application/MovementService";
import {Movement} from "@common/api/domain/Movement";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMovements({perPage = 10} = {}) {
    const service = new MovementService();

    return useInfiniteQuery<
        ServiceResponse<Movement[]>,
        Error,
        InfiniteData<ServiceResponse<Movement[]>>,
        string[],
        number
    >({
        queryKey: ["movementsList"],
        queryFn: async ({pageParam = 1}) => service.fetchMovements({page: pageParam, perPage}),
        getNextPageParam: (lastPage) => {
            if (!lastPage.pagination) return undefined;
            const currentPage = lastPage.pagination.page;
            const totalPages = Math.ceil(lastPage.pagination.count / lastPage.pagination.perPage);
            return currentPage < totalPages ? currentPage + 1 : undefined;
        },
        initialPageParam: 1,
    });
}
