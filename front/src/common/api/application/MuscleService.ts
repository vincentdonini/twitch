import {fetchMuscleById, fetchMuscles} from "@common/api/infrastructure/MuscleApi";
import {Muscle, MuscleGroup, Tree} from "@common/api/domain/Muscle";
import {ServiceResponse} from "@common/api/application/types/ServiceResponse";
import {BaseService} from "@common/api/application/BaseService";

interface FetchMusclesParams {
    page?: number;
    perPage?: number;
    pagination?: boolean;
}

export class MuscleService extends BaseService {
    async fetchMuscles({page = 1, perPage = 10, pagination = true}: FetchMusclesParams = {}): Promise<ServiceResponse<Muscle[]>> {
        try {
            const {data, pagination} = await fetchMuscles({page, perPage});
            return this.handleSuccess(data, pagination);
        } catch (e) {
            return this.handleError<Muscle[]>(e);
        }
    }

    async fetchMuscleById(id: number): Promise<ServiceResponse<Muscle>> {
        try {
            const data = await fetchMuscleById(id);
            return this.handleSuccess(data);
        } catch (e) {
            return this.handleError<Muscle>(e);
        }
    }

    static organizeMuscles(muscles: Muscle[]): Tree {
        const tree: Tree = {};

        muscles.forEach((muscle) => {
            const area = muscle.area;

            const group: MuscleGroup = muscle.group ?? {
                id: 0,
                slug: 'ungrouped',
                name: 'Ungrouped',
                summary: '',
                details: '',
            };

            if (!tree[area.id]) {
                tree[area.id] = {...area, groups: {}};
            }

            if (!tree[area.id].groups[group.id]) {
                tree[area.id].groups[group.id] = {...group, muscles: []};
            }

            tree[area.id].groups[group.id].muscles.push(muscle);
        });

        return tree;
    }
}
