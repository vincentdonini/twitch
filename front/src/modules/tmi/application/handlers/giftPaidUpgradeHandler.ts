import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {SubGiftUpgradeUserstate} from "tmi.js";

const debug = true;
const muteBot = true;

export function handleGiftPaidUpgrade(
    channel: string,
    username: string,
    sender: string,
    userstate: SubGiftUpgradeUserstate,
) {
    const chatMessage = `Merci @${sender} pour l'upgrade de l'abonnement de${username}!`;

    if (debug) {
        console.log('[handleGiftPaidUpgrade]', {
            channel,
            username,
            sender,
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
