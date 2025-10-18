import {fetchWodVersionTypeById, fetchWodCategories} from "@common/api/infrastructure/WodApi";
import {WodVersionType} from "@common/api/domain/Wod";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

export class WodVersionTypeService extends BaseService {
    async fetchWodVersionTypes(): Promise<ServiceResponse<WodVersionType[]>> {
        try {
            const {data, pagination} = await fetchWodCategories();
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<WodVersionType[]>(e);
        }
    }

    async fetchWodVersionTypeById(id: number): Promise<ServiceResponse<WodVersionType>> {
        try {
            const data = await fetchWodVersionTypeById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<WodVersionType>(e);
        }
    }
}