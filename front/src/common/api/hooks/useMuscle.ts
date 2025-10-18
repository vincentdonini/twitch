import {useQuery} from "@tanstack/react-query";
import {MuscleService} from "@common/api/application/MuscleService";
import {Muscle} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMuscle(id?: number) {
    return useQuery<ServiceResponse<Muscle>>({
        queryKey: ["muscle", id],
        queryFn: async () => {
            if (!id) throw new Error("Invalid muscle ID");
            const service = new MuscleService();
            return service.fetchMuscleById(id);
        },
        enabled: !!id,
    });
}
