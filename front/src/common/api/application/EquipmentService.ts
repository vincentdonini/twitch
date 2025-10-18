import {fetchEquipmentById, fetchEquipments} from "@common/api/infrastructure/EquipmentApi";
import {Equipment} from "@common/api/domain/Equipment";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

interface FetchEquipmentsParams {
    page?: number;
    perPage?: number;
    pagination?: boolean;
}

export class EquipmentService extends BaseService {
    async fetchEquipments({page = 1, perPage = 10, pagination = true}: FetchEquipmentsParams = {}): Promise<ServiceResponse<Equipment[]>> {
        try {
            const {data, pagination} = await fetchEquipments({page, perPage});
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<Equipment[]>(e);
        }
    }

    async fetchEquipmentById(id: number): Promise<ServiceResponse<Equipment>> {
        try {
            const data = await fetchEquipmentById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<Equipment>(e);
        }
    }
}