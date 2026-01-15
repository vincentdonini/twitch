import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {queueAlert} from "@modules/tmi/application/commands/queueAlert";
import {store} from "@stores/stores";

const debug = true;
const muteBot = false;

export function handleRaided(
    channel: string,
    username: string,
    viewers: number,
) {
    const chatMessage = `Merci pour le raid @${username} de ${viewers} spectateur(s)!`;

    if (debug) {
        console.log(`[handleRaided]`, {
            channel,
            username,
            viewers,
            chatMessage,
        });
    }

    queueAlert(store.dispatch, {
        type: "raided",
        username: username,
        message: chatMessage,
        timestamp: Date.now(),
    });

    if (!muteBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}
