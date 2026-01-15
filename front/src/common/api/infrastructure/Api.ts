import {apiFetch} from "@common/api/infrastructure/client";
import {User} from "@common/api/domain/User";

export class Api {
    static async getUsers(): Promise<User[]> {
        return await apiFetch('/fr/users');
    }

    // static async postEvent(event: {
    //     type: string;
    //     userId: string;
    //     username: string;
    //     amount?: number;
    // }): Promise<void> {
    //     await apiFetch<Event>('/fr/events', {
    //         method: 'POST',
    //         body: JSON.stringify(event),
    //     });
    // }
}
