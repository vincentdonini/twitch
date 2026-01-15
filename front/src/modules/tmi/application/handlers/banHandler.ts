import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {BanUserstate} from "tmi.js";

const debug = true;
const muteBot = false;

const messages = [
    "BYE-BYE @{username} !",
    "@{username}... C'est ciao !",
    "Cheh @{username}... Gros CHEH !",
    "Oh non @{username}, t'as été banni 😱",
    "RIP @{username}, c'était sympa de te connaître 👋 ( ou pas )",
    "Aïe aïe aïe @{username}, c'est un ban direct !",
    "@{username} s'est fait atomiser par le banhammer 🔨",
    "Game over @{username} 🎮 ! Merci d'avoir joué.",
    "@{username} vient de se faire delete... 💀",
    "Un instant de silence pour @{username}... Ah non en fait, next. 🙃",
    "Hop hop hop @{username}, direction la sortie 🚪",
    "@{username} a pris un aller simple pour le banland ✈️",
    "@{username}, on t'avait prévenu... BAN. 😈",
    "Ce n'est qu'un au revoir @{username}... ou pas 😏",
    "Le tribunal du chat a statué : @{username} est coupable ! ⚖️",
    "Et BOOM @{username} 💥 Ça dégage !",
    "Un petit tour et puis s'en va @{username} 🎭",
    "@{username}, t'as gagné un ticket pour le mode spectateur 🎟️",
    "F pour @{username} dans le chat 💀",
    "@{username}, c'était une erreur de ta part... et une bénédiction pour nous 😈",
    "Le karma a frappé @{username} ⚡",
    "Il ne reste plus que l'ombre de @{username} 👻",
    "Nous ne parlerons plus jamais de @{username}... ou alors juste pour se moquer 😜",
    "@{username}, tu as été le maillon faible... AU REVOIR ! 😈",
    "Mission échouée @{username}, on te reverra peut-être dans une autre vie.",
    "@{username}, tu viens d’entrer dans la légende… des bannis. 🎭",
    "@{username}, t'as trouvé la sortie secrète du chat... Bravo ? 🤔",
    "Le mode spectateur est activé pour @{username}. 🎥",
    "Pssst... @{username}... personne ne se souviendra de toi. 👀",
    "Désolé @{username}, mais ici ce n'est pas chez mamie. 🚪",
    "On a trouvé un bug dans la matrice, c'était @{username}. Expulsé ! 🕶️",
    "Une porte se ferme... mais aucune ne s’ouvre pour @{username}. 🚷",
    "Et c'est un home run pour le bot de modération ! @{username} est OUT ! ⚾",
    "Les dieux du chat ont parlé. @{username}, tu n’es pas digne ! ⚡",
    "@{username} a pris la pilule rouge... et a disparu. 🔴",
    "Ce n'est pas toi, @{username}... enfin si, c'est bien toi le problème. 👋",
    "@{username}, ton temps de parole est écoulé. 🔇",
    "Accès refusé. @{username}, votre présence est une erreur système. ⛔",
    "Désolé @{username}, la connexion avec ce chat a été perdue. 📶",
    "@{username}, ton abonnement au chat a expiré. Merci de ne pas revenir. 🔚"
];

export function handleBan(
    channel: string,
    username: string,
    reason: string,
    userstate: BanUserstate,
) {
    const template = messages[Math.floor(Math.random() * messages.length)];
    const chatMessage = template
        .replace(/\{username\}/g, username)
        .replace(/\{reason\}/g, reason);

    if (debug) {
        console.log(`[handleBan]`, {
            channel,
            username,
            reason,
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
