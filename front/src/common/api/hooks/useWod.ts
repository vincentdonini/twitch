import {useQuery} from "@tanstack/react-query";
import {WodService} from "@common/api/application/WodService";
import {Wod} from "@common/api/domain/Wod";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useWod(id?: number) {
    return useQuery<ServiceResponse<Wod>>({
        queryKey: ["muscle", id],
        queryFn: async () => {
            if (!id) throw new Error("Invalid wod ID");
            const service = new WodService();
            return service.fetchWodById(id);
        },
        enabled: !!id,
    });
}
