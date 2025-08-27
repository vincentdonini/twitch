import {ChatUserstate} from "tmi.js";
import {store} from "@stores/stores";
import {addMessage, deleteMessage} from "@stores/chatSlice";
import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {BLOCKED_WORDS} from "@modules/tmi/domain/constants/BlockedWords";
import {CommandHandler} from "@modules/tmi/domain/handlers/ChatMessageHandler";
import {setLastFollow} from "@modules/tmi/application/store/engagementSlice";
import {setLastSubscription} from "@modules/twitch/context/engagementSlice";

const debug = true;
const muteBot = false;

const TWITCH_BOT_NAME = process.env.NEXT_PUBLIC_TWITCH_BOT_NAME!;

export async function handleMessage(channel: string, userstate: ChatUserstate, message: string, self: boolean) {
    if (self) return;

    if (userstate.username?.toLowerCase() === TWITCH_BOT_NAME) {
        checkBotMessage(message);
        // return;
    }

    if (self || userstate.id) {
        const twitchMessage: ITwitchMessage = {
            id: userstate.id || "00000",
            username: userstate.username || "Unknown",
            channel: channel,
            message,
        };

        if (debug) {
            console.log(`[handleMessage]`, {
                channel,
                userstate,
                message,
                self,
                twitchMessage,
            });
        }

        if (!muteBot) {
            checkTwitchMessage(twitchMessage);
            checkCommand(twitchMessage);
        }
    }
}

function checkTwitchMessage(twitchMessage: ITwitchMessage) {
    const message = twitchMessage.message;

    const shouldSendMessage = BLOCKED_WORDS.some(blockedWord => message.includes(blockedWord.toLowerCase()));

    if (shouldSendMessage && twitchMessage.id) {
        store.dispatch(deleteMessage(twitchMessage));
    } else {
        store.dispatch(addMessage(twitchMessage));
    }
}

function checkCommand(twitchMessage: ITwitchMessage) {
    const commandService = new CommandHandler();
    commandService.handleCommand(twitchMessage);
}

function checkBotMessage(message: string) {
    if (message.includes('New Follower')) {
        // 👤 New Follower : #follow_name# (#follow_count# followers)
        const match = message.match(/New Follower\s*:\s*([\w.-]+)\s*\(/);
        if (!match) return;

        const username = match[1];
        store.dispatch(setLastFollow(username));
        return;
    }

    if (message.includes('New Subscriber')) {
        // ⭐ New Subscriber : #display_name# (#sub_count# subcribers)
        const match = message.match(/New Subscriber\s*:\s*([\w.-]+)\s*\(/);
        if (!match) return;

        const username = match[1];
        store.dispatch(setLastSubscription(username));
        return;
    }

    if (message.includes('New Gift Subscriber')) {
        // ⭐ New Gift Subscriber : #display_name# (#sub_count# subcribers - 🎁 Offert par #gift_by#)
        const match = message.match(/New Gift Subscriber\s*:\s*([\w.-]+)\s*\(/);
        if (!match) return;

        const username = match[1];
        store.dispatch(setLastSubscription(username));
        return;
    }

    if (message.includes('New Prime Subscriber')) {
        // ⭐ New Prime Subscriber : #display_name# (#sub_count# subcribers)
        const match = message.match(/New Prime Subscriber\s*:\s*([\w.-]+)\s*\(/);
        if (!match) return;

        const username = match[1];
        store.dispatch(setLastSubscription(username));
        return;
    }
}
