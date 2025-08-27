import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {ADMIN_USERS, MODERATOR_USERS, VIP_USERS} from "@common/constants/users";
import {selectActiveSubscriptions} from "@stores/selectors";
import {store} from "@stores/stores";
import {handleVainceAllay, handleVainceGGBison} from "@modules/tmi/domain/commands/viewerHandlers/vainceHandlers";

const subscribers = selectActiveSubscriptions(store.getState());

export const vainceCommands: Record<string, CommandDefinition> = {
    "vainceAllay": {
        action: handleVainceAllay,
        allowedUsers: [...ADMIN_USERS, ...MODERATOR_USERS, ...VIP_USERS, ...subscribers],
        matchType: 'includes',
    },
    "vainceGGBison": {
        action: handleVainceGGBison,
        allowedUsers: [...ADMIN_USERS, ...MODERATOR_USERS, ...VIP_USERS, ...subscribers],
        matchType: 'includes',
    },
};
