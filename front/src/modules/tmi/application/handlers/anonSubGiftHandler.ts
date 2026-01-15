import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {AnonSubGiftUserstate, SubMethods} from "tmi.js";

const debug = true;
const muteBot = true;

export function handleAnonSubGift(
    channel: string,
    streakMonths: number,
    recipient: string,
    methods: SubMethods,
    userstate: AnonSubGiftUserstate,
) {
    const chatMessage = `Merci à l'anonyme pour le sub offert à ${recipient}!`;

    if (debug) {
        console.log('[handleAnonSubGift]', {
            channel,
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
