import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {handlePokPikachu} from "@modules/tmi/domain/handlers/viewer/deltekHandlers";

const OWNER =  "deltekk";

export const deltekCommands: Record<string, CommandDefinition> = {
    "PokPikachu": {
        action: handlePokPikachu,
        allowedUsers: [OWNER, "vaince_woder", "manugraph"],
        matchType: 'includes',
    },
};
