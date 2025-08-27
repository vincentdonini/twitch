import {queueSound} from "@modules/tmi/application/commands/queueSound";
import {store} from "@stores/stores";
import {CommandDefinition} from "@modules/tmi/domain/types/commandTypes";

export const funCommands: Record<string, CommandDefinition> = {
    "...": {
        action: (msg) => {
            // LOG
            console.log(`[handleCommand] ${msg.username} a utilisé "troisPointsDeSuspension" ${msg.message}`);

            // SOUND
            queueSound(store.dispatch, {
                url: '/sounds/trois-points-de-suspension.mp3',
                duration: 4000,
                volume: 1,
                timestamp: Date.now(),
            });

            // TEXT
            // twitchBot.sendMessage(`teukos240 !!!`);
        },
        matchType: 'endsWith',
    },
};