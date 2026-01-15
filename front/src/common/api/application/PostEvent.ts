import {Api} from "@common/api/infrastructure/Api";

export async function postEvent(type: string, userId: string, username: string, amount?: number) {
    return await Api.postEvent({
        type,
        userId,
        username,
        amount,
    });
}