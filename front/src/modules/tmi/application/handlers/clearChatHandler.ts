import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";

const debug = true;
const muteBot = false;

const messages = [
    "🌪️ Le tchat vient de se faire balayer !",
    "🚫 Tchat purgé. RIP les messages.",
    "💣 Boum. Tchat anéanti.",
    "🛑 Chat reset. Nouvelle ère.",
    "📦 Vos messages ont été rangés dans un carton au fond du garage.",
    "🔄 Mise à jour du tchat : 100% clean.",
    "🚽 On a tiré la chasse du tchat.",
    "🕳️ Tous les messages sont tombés dans un trou noir.",
    "✨ Le sort \"Purge Totale\" a été lancé avec succès.",
    "🪄 Les messages ont été désintégrés par magie.",
    "🔥 Tous les messages ont été sacrifiés au dieu du tchat.",
    "⏳ Le temps a été réinitialisé. Les mots ont disparu.",
    "> system(\"clearChat\") executed successfully.",
    "console.log('Chat cleaned ✔️');",
    "404 messages not found.",
    "[TCHAT RESET DETECTED]",
];

export function handleClearChat(
    channel: string,
) {
    const chatMessage = messages[Math.floor(Math.random() * messages.length)];

    if (debug) {
        console.log(`[handleClearChat]`, {
            channel,
            chatMessage,
        });
    }

    if (!muteBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}
