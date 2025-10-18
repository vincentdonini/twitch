import {fetchWodCategoryById, fetchWodCategories} from "@common/api/infrastructure/WodApi";
import {WodCategory} from "@common/api/domain/Wod";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

export class WodCategoryService extends BaseService {
    async fetchWodCategories(): Promise<ServiceResponse<WodCategory[]>> {
        try {
            const {data, pagination} = await fetchWodCategories();
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<WodCategory[]>(e);
        }
    }

    async fetchWodCategoryById(id: number): Promise<ServiceResponse<WodCategory>> {
        try {
            const data = await fetchWodCategoryById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<WodCategory>(e);
        }
    }
}