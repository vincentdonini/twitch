import {ChatUserstate} from "tmi.js";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {queueAlert} from "@modules/tmi/application/commands/queueAlert";
import {store} from "@stores/stores";
import {setLastDonor, setTopDonor} from "@modules/tmi/application/store/engagementSlice";
import {postEvent} from "@common/api/application/PostEvent";
import {getTopDonor} from "@common/api/application/GetTopDonor";

const debug = true;
const muteBot = false;

const messages = [
    "Merci @{username} pour les {bits} bits ! La légende continue 🔥",
    "@{username}, tu viens de faire briller le stream avec {bits} bits ✨",
    "{bits} bits de pur amour de la part de @{username} 💜",
    "On applaudit bien fort @{username} pour ses {bits} bits ! 👏👏",
    "{bits} bits... wow ! @{username}, t'es une vraie machine à générosité 💸",
    "La pluie de bits commence grâce à @{username} ! Merci pour les {bits} bits 💦",
    "@{username} a drop {bits} bits ! Vous avez vu son niveau de générosité !",
    "Merci @{username} pour les {bits} bits ! C’est pas un stream, c’est une fête 🎉",
    "Ooooooh ! @{username} envoie {bits} bits, et c’est le feu dans le chat 🔥🔥"
];

export function handleCheer(
    channel: string,
    userstate: ChatUserstate,
    message: string,
) {
    const username = userstate.username!;
    const bits = userstate.bits!;

    const template = messages[Math.floor(Math.random() * messages.length)];
    const chatMessage = template
        .replace(/\{username\}/g, username)
        .replace(/\{bits\}/g, bits);

    if (debug) {
        console.log(`[handleCheer]`, {
            channel,
            userstate,
            message,
            username,
            bits,
            chatMessage,
        });
    }

    queueAlert(store.dispatch, {
        type: "cheer",
        username: username,
        message: `vient de lâcher ${bits} bit(s)`,
        timestamp: Date.now(),
    });

    store.dispatch(setLastDonor(username));

    if (!muteBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}

/*

[handleCheerEvent]
ChatUserstate: {"badge-info":null,"badges":{"bits":"100"},"bits":"100","color":"#1E90FF","display-name":"NetriAlex","emotes":null,"first-msg":false,"flags":null,"id":"e4825fca-5174-409d-807e-8922a8f6f839","mod":false,"returning-chatter":false,"room-id":"50597026","subscriber":false,"tmi-sent-ts":"1743526710573","turbo":false,"user-id":"123231190","user-type":null,"emotes-raw":null,"badge-info-raw":null,"badges-raw":"bits/100","username":"netrialex","message-type":"chat"}
Message: Cheer100

[handleCheerEvent]
ChatUserstate: {"badge-info":{"subscriber":"1"},"badges":{"subscriber":"0","hype-train":"1"},"bits":"100","color":"#8A2BE2","display-name":"soll2k25","emotes":null,"first-msg":false,"flags":null,"id":"f7151590-191f-4a01-996b-a5803a632c50","mod":false,"returning-chatter":false,"room-id":"135468063","subscriber":true,"tmi-sent-ts":"1743618348156","turbo":false,"user-id":"1277280889","user-type":null,"emotes-raw":null,"badge-info-raw":"subscriber/1","badges-raw":"subscriber/0,hype-train/1","username":"soll2k25","message-type":"chat"}
Message: c’est quoi ton plat préféré alors ? Kappa100 - ChatMessage: Merci @soll2k25 pour les 100 bit(s)!

[handleCheerEvent]
ChatUserstate: {"badge-info":null,"badges":{"premium":"1"},"bits":"100","color":"#0000FF","display-name":"Urban_225","emotes":null,"first-msg":false,"flags":null,"id":"ebabb2b1-49cf-4ba9-afb1-52332ebd34af","mod":false,"returning-chatter":false,"room-id":"68078157","subscriber":false,"tmi-sent-ts":"1743618545519","turbo":false,"user-id":"29833651","user-type":null,"emotes-raw":null,"badge-info-raw":null,"badges-raw":"premium/1","username":"urban_225","message-type":"chat"} - Message: Cheer100 Ce stream tourne à 60 FPS en 4K ray tracing… c’est plus fluide qu’une Switch 2 overclockée par Miyamoto lui-même. RÉVO-LI-TION.
ChatMessage: Merci @urban_225 pour les 100 bit(s)!

*/
