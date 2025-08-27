import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {AnonSubGiftUpgradeUserstate} from "tmi.js";

const debug = true;
const muteBot = true;

export function handleAnonGiftPaidUpgrade(
    channel: string,
    username: string,
    userstate: AnonSubGiftUpgradeUserstate,
) {
    const chatMessage = `Merci à l'anonyme pour l'upgrade de l'abonnement de @${username}!`;

    if (debug) {
        console.log('[handleAnonGiftPaidUpgrade]', {
            channel,
            username,
            userstate,
            chatMessage,
        });
    }

    if(!muteBot){
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}
