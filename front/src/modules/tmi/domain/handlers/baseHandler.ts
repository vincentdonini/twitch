import {store} from "@stores/stores";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {ITwitchMessage} from "@modules/tmi/domain/interfaces/ITwitchMessage";
import {commandRegistry} from "@modules/tmi/domain/commands/commandsRegistry";

export const handleMusic = async (msg: ITwitchMessage) => {
    const activeMusic = store.getState().spotify.activeMusic;
    const username = msg.username!;

    let chatMessage = "";

    if (!activeMusic) {
        const messages = [
            "🎧 Je n'écoute rien en ce moment... mais tu sais @{username}, le silence, c'est parfois de l'art.",
            "🙉 Rien en cours de lecture pour l'instant @{username}... mais t’as une reco ? 👀",
            "🧘‍♂️ Aucun son pour le moment... Ca fait du bien parfois, pas vrai @{username} ?",
            "😴 Pas de musique en ce moment. On est en pleine sieste auditive",
            "🚫 Aucun morceau détecté. Peut-être que Spotify dort aussi, @{username} 😅",
            "🎵 Silence radio ! Mais t’en fais pas @{username}, la musique revient toujours.",
            "📻 Pas de son pour l’instant... mais t’inquiète, ça va repartir fort @{username} !",
            "🤖 Mon capteur musical n'entend rien... @{username}, tu entends quelque chose toi ?",
            "🎶 Aucun morceau à afficher... et si c’était l’occasion d’écouter du bon vieux classique @{username} ? 😉",
            "🕶️ Pas de musique active en ce moment... désolé @{username}."
        ];

        const template = messages[Math.floor(Math.random() * messages.length)];
        chatMessage = template
            .replace(/\{username\}/g, username);
    } else {
        chatMessage = `🎵 En écoute: « ${activeMusic.music} » par ${activeMusic.artist} (album : ${activeMusic.album}, ${activeMusic.year}) → ${activeMusic.url}`;
    }

    twitchBot
        .sendMessage(chatMessage)
        .then(() => {});
};

export const handleCommands = async () => {
    const availableCommands = Object.keys(commandRegistry).filter(cmd => cmd !== '!help');
    const message = `Voici mes commandes : ${availableCommands.join(' | ')}. Tape !help [commande] pour plus d'infos.`;

    await twitchBot.sendMessage(message);
};

export const handleHelp = async (msg: ITwitchMessage) => {
    const args = msg.message.trim().split(' ');
    const commandRequested = args[1];

    console.log('handleHelp', args);

    if (!commandRequested || !commandRegistry[commandRequested]) {
        await twitchBot.sendMessage(
            `Commande inconnue ou manquante. Usage : !help [commande] (ex: !help !music)`
        );
        return;
    }

    const description = commandRegistry[commandRequested].description;
    await twitchBot.sendMessage(`${commandRequested} : ${description}`);
};