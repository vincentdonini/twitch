import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {handleVegaMissyl} from "@modules/tmi/domain/handlers/viewer/theledaxHandlers";

const OWNER =  "the_ledax";

export const theledaxCommands: Record<string, CommandDefinition> = {
    "Vega Missyl": {
        action: handleVegaMissyl,
        allowedUsers: [OWNER, "vaince_woder"],
        matchType: 'includes',
    },
};
