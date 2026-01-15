// NOTE : domain/commands/ — "Ce qu'on veut faire"
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";
import {handleCommands, handleHelp, handleMusic} from "@modules/tmi/domain/handlers/baseHandler";

export const baseCommands: Record<string, CommandDefinition> = {
    "!commands": {
        action: handleCommands,
        matchType: 'strict',
    },
    "!help": {
        action: handleHelp,
        matchType: 'regex',
    },
    "!music": {
        action: handleMusic,
        matchType: 'strict',
    },
    "!hello": {
        action: (twitchMessage) => {
            twitchBot
                .sendMessage(`Bonjour ${twitchMessage.username} !`)
                .then(() => {});
        },
        matchType: 'strict',
    },
    "!startsWith": {
        action: (msg) => {
            console.log(`[startsWith] ${msg.message}`);
        },
        matchType: 'startsWith',
    },
    "!endsWith": {
        action: (msg) => {
            console.log(`[endsWith] ${msg.message}`);
        },
        matchType: 'endsWith',
    },
    "!includes": {
        action: (msg) => {
            console.log(`[includes] ${msg.message}`);
        },
        matchType: 'includes',
    },
    "!strict": {
        action: (msg) => {
            console.log(`[strict] ${msg.message}`);
        },
        matchType: 'strict',
    },
};
