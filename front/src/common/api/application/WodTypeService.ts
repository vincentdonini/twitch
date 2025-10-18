import {fetchWodTypeById, fetchWodTypes} from "@common/api/infrastructure/WodApi";
import {WodType} from "@common/api/domain/Wod";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

export class WodTypeService extends BaseService {
    async fetchWodTypes(): Promise<ServiceResponse<WodType[]>> {
        try {
            const {data, pagination} = await fetchWodTypes();
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<WodType[]>(e);
        }
    }

    async fetchWodTypeById(id: number): Promise<ServiceResponse<WodType>> {
        try {
            const data = await fetchWodTypeById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<WodType>(e);
        }
    }
}