import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";

const debug = true;
const muteBot = false;

export function handleHosted(
    channel: string,
    username: string,
    viewers: number,
    autohost: boolean,
) {
    const chatMessage = `Merci @${username} pour l'hébergement de ${viewers} téléspectateurs!`;

    if (debug) {
        console.log(`[handleHosted]`, {
            channel,
            username,
            viewers,
            autohost,
            chatMessage,
        });
    }

    if (!muteBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}
