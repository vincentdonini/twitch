import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {handleWimjaGg, handleWimjaKeur} from "@modules/tmi/domain/handlers/viewer/wimjaHandlers";

export const wimjaCommands: Record<string, CommandDefinition> = {
    "wimjaGg": {
        action: handleWimjaGg,
        matchType: 'includes',
    },
    "wimjaKeur": {
        action: handleWimjaKeur,
        matchType: 'includes',
    },
};
