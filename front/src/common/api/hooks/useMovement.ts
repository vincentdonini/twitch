import { useQuery } from "@tanstack/react-query";
import { MovementService } from "@common/api/application/MovementService";
import { Movement } from "@common/api/domain/Movement";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useMovement(id?: number) {
    return useQuery<ServiceResponse<Movement>>({
        queryKey: ["movements", id],
        queryFn: async () => {
            if (!id) throw new Error("Invalid movement ID");
            const service = new MovementService();
            return service.fetchMovementById(id);
        },
        enabled: !!id,
    });
}
