import {ADMIN_USERS, MODERATOR_USERS} from "@common/constants/users";
import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {handleKomodoHype, handleShikam3Kakas} from "@modules/tmi/domain/handlers/emoteHandler";

export const emoteCommands: Record<string, CommandDefinition> = {
    "KomodoHype": {
        order: 1,
        action: handleKomodoHype,
        allowedUsers: [...ADMIN_USERS, ...MODERATOR_USERS],
        matchType: 'includes',
    },
    "PogChamp": {
        order: 1,
        action: handleKomodoHype,
        allowedUsers: [...ADMIN_USERS, ...MODERATOR_USERS],
        matchType: 'includes',
    },
    "shikam3Kakas": {
        action: handleShikam3Kakas,
        matchType: 'includes',
    },
};