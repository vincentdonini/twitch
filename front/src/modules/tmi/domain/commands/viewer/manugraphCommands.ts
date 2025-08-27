import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {handleILoveYou, handleTriste} from "@modules/tmi/domain/handlers/viewer/manugraphHandlers";

const OWNER =  "manugraph";

export const manugraphCommands: Record<string, CommandDefinition> = {
    "!triste": {
        action: handleTriste,
        allowedUsers: [OWNER, "vaince_woder"],
        matchType: 'strict',
    },
    "<3": {
        action: handleILoveYou,
        allowedUsers: [OWNER, "vaince_woder"],
        matchType: 'strict',
    },
};
