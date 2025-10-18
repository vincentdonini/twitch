import {useQuery} from "@tanstack/react-query";
import {EquipmentService} from "@common/api/application/EquipmentService";
import {Equipment} from "@common/api/domain/Equipment";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";

export function useEquipment(id?: number) {
    return useQuery<ServiceResponse<Equipment>>({
        queryKey: ["equipment", id],
        queryFn: async () => {
            if (!id) throw new Error("Invalid equipment ID");
            const service = new EquipmentService();
            return service.fetchEquipmentById(id);
        },
        enabled: !!id,
    });
}
