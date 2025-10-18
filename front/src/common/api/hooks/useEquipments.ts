import {useInfiniteQuery, InfiniteData} from "@tanstack/react-query";
import {EquipmentService} from "@common/api/application/EquipmentService";
import {Equipment} from "@common/api/domain/Equipment";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useEquipments({perPage = 10} = {}) {
    const service = new EquipmentService();

    return useInfiniteQuery<
        ServiceResponse<Equipment[]>,
        Error,
        InfiniteData<ServiceResponse<Equipment[]>>,
        string[],
        number
    >({
        queryKey: ["equipmentsList"],
        queryFn: async ({pageParam = 1}) => service.fetchEquipments({page: pageParam, perPage}),
        getNextPageParam: (lastPage) => {
            if (!lastPage.pagination) return undefined;
            const currentPage = lastPage.pagination.page;
            const totalPages = Math.ceil(lastPage.pagination.count / lastPage.pagination.perPage);
            return currentPage < totalPages ? currentPage + 1 : undefined;
        },
        initialPageParam: 1,
    });
}
