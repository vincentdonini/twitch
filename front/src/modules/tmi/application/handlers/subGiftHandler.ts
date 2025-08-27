import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {SubGiftUserstate, SubMethods} from "tmi.js";

const debug = true;
const muteBot = true;

export function handleSubGift(
    channel: string,
    username: string,
    streakMonths: number,
    recipient: string,
    methods: SubMethods,
    userstate: SubGiftUserstate,
) {
    const chatMessage = `Merci @${username} pour le sub offert à @${recipient}!`;

    if (debug) {
        console.log('[handleSubGift]', {
            channel,
            username,
            streakMonths,
            recipient,
            methods,
            userstate,
            chatMessage,
        });
    }

    if (!muteBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}
