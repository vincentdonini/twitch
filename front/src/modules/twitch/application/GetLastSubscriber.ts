import {ITwitchService} from "@modules/twitch/application/ITwitchService";
import {UserIdentifier} from "@common/api/domain/UserIdentifier";

export class GetLastSubscriber {
    constructor(private service: ITwitchService) {}

    async execute(): Promise<UserIdentifier> {
        return await this.service.getLastSubscriber();
    }
}
