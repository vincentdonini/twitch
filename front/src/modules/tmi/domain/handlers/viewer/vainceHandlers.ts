import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";

export const handleVainceAllay = (msg: ITwitchMessage) => {
    console.log(`[handleCommand] ${msg.username} a utilisé "vainceAllay" ${msg.message}`);
    // twitchBot.sendMessage(`vainceAllay !!!`);
};

export const handleVainceGGBison = (msg: ITwitchMessage) => {
    console.log(`[handleCommand] ${msg.username} a utilisé "vainceGGBison" ${msg.message}`);
    // twitchBot.sendMessage(`vainceGGBison !!!`);
};