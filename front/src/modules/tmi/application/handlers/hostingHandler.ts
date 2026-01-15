import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";

const debug = true;
const muteBot = false;

export function handleHosting(
    channel: string,
    target: string,
    viewers: number,
) {
    const chatMessage = `Nous hébergeons désormais @${target} avec ${viewers} spectateurs!`;

    if (debug) {
        console.log(`[handleHosting]`, {
            channel,
            target,
            viewers,
            chatMessage,
        });
    }

    if (!muteBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}
