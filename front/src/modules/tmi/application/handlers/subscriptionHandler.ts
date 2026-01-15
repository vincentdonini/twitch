import {SubMethods, SubUserstate} from "tmi.js";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {queueAlert} from "@modules/tmi/application/commands/queueAlert";
import {store} from "@stores/stores";
import {setLastSubscription} from "@modules/tmi/application/store/engagementSlice";
import {postEvent} from "@common/api/application/PostEvent";

const debug = true;
const muteBot = false;

const classicMessages = [
    "Bienvenue à @{username} dans la team des abos 💜!",
    "@{username} vient de s’abonner ! Fais comme chez toi 😎.",
    "Un gros GG à @{username} pour le sub 🔥.",
    "Merci @{username} pour le sub ! Place au confort niveau premium 🛋️.",
    "@{username}, tu viens d’entrer dans le cercle très fermé des abos ✨.",
    "Oh oui ! @{username} s’abonne et ça, ça fait plaisir 🙌!",
    "Un sub tout neuf pour @{username}. Bienvenue à toi!",
    "Merci @{username} ! Ton sub illumine ce stream 🌟",
    "@{username}, tu viens de faire le bon choix 💜. Merci pour l’abonnement !",
    "@{username} a cliqué sur le bouton \"S’abonner\", et bim... C'est dingue !",
    "@{username} s’abonne ! Comme quoi, tout arrive 👀.",
    "Merci @{username}, ton sub va directement dans le budget croquettes 🐱",
    "Encore un esclave de Twitch Prime ? Ah non... C’est un vrai sub de @{username} 😳!",
    "@{username} a dit OUI au sub, mais NON à la pub. Respect ✊",
    "@{username} s’abonne. Ça mérite une petite danse (que je ne ferai pas).",
    "@{username} vient de sub. Est-ce que c’était une erreur ? De toute façon c'est trop tard maintenant ! Bisous.",
    "Un abonnement et hop, @{username} rejoint l'élite avec panache!",
    "Bien joué @{username} ! T’as pris un abonnement... mais maintenant, tu vas devoir supporter nos blagues pour les 30 prochains jours 😅",
    "@{username} a sorti la CB ! Là on parle de vrai soutien 💪. Un grand merci !",
    "@{username}, claque un abonnement ! Pas de Prime ici, juste du 💸 et du ❤️. Merci !",
];

const primeMessages = [
    "@{username} a lâché son Prime et as bien fait... Surtout pour échapper à la pub 😁.",
    "@{username} a réveillé son Prime ! Bezos a perdu un peu d'argent, et nous on a gagné un vrai soutien!",
    "Hey @{username}, merci d’avoir empêché ton Prime de dormir un mois de plus 😴💜.",
    "@{username} a fait le bon choix : \"Un Prime bien utilisé, c’est un Prime chez nous !\"",
    "@{username} a transformé un Prime oublié en un geste de légende 💪.",
    "T’as retrouvé ton Prime dans le fond d’un tiroir, hein @{username} ? Heureusement que t’es tombé sur nous 😏.",
    "Merci @{username} ! Ton Prime ne finira pas à la casse ce mois-ci, et ça, c’est beau ✨",
    "Ahhh @{username}, un \"Prime\"... Bon choix, même si on sait que c'est surtout pour éviter les pubs 😅",
    "Avec ton Prime, @{username}, c’est comme si tu ne voulais pas trop dépenser, mais t’es quand même là pour soutenir 💪!",
    "Le Prime de @{username} a enfin servi à autre chose qu’à rater des colis. Merci 😄.",
    "Un Prime utilisé, c’est un Prime sauvé. Merci @{username}, j'en prendrai soin!",
];

export function handleSubscription(
    channel: string,
    username: string,
    methods: SubMethods,
    message: string,
    userstate: SubUserstate
) {
    const isPrime = methods["prime"];

    const messages = isPrime ? primeMessages : classicMessages;
    const template = messages[Math.floor(Math.random() * messages.length)];

    const chatMessage = template
        .replace(/\{username\}/g, username);

    if (debug) {
        console.log(`[handleSubscription]`, {
            channel,
            username,
            methods,
            message,
            userstate,
            chatMessage,
        });
    }

    queueAlert(store.dispatch, {
        type: "subscription",
        username: username,
        message: `vient de s'abonner`,
        timestamp: Date.now(),
    });

    store.dispatch(setLastSubscription(username));
    postEvent('subscription', userstate['user-id']!, username).then();

    if (!muteBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}

/*

[handleSubscription]
Username: miss_narouxi
SubMethods: {"prime":true,"plan":"Prime","planName":"Les gens gentils qui supportent Laink"}
SubUserstate: {"badge-info":{"subscriber":"1"},"badges":{"subscriber":"0","premium":"1"},"color":"#8A2BE2","display-name":"miss_narouxi","emotes":null,"flags":null,"id":"5232ee44-54ab-47e5-989b-973820a55caa","login":"miss_narouxi","mod":false,"msg-id":"sub","msg-param-cumulative-months":true,"msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Les gens gentils qui supportent Laink","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"89872865","subscriber":true,"system-msg":"miss_narouxi subscribed with Prime.","tmi-sent-ts":"1743617151667","user-id":"471010942","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/1","badges-raw":"subscriber/0,premium/1","message-type":"sub"}
Message: null
ChatMessage: Merci @miss_narouxi de t'être abonné !

[handleSubscription]
Username: TonyLar
SubMethods: {"prime":true,"plan":"Prime","planName":"Random péon "}
SubUserstate: {"badge-info":{"subscriber":"1"},"badges":{"subscriber":"0","premium":"1"},"color":"#D2691E","display-name":"TonyLar","emotes":null,"flags":null,"id":"73bbe4a0-2f4b-4a0d-9ae3-22ebcc6a4533","login":"tonylar","mod":false,"msg-id":"sub","msg-param-cumulative-months":true,"msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Random péon ","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"68078157","subscriber":true,"system-msg":"TonyLar subscribed with Prime.","tmi-sent-ts":"1743617372100","user-id":"57555082","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/1","badges-raw":"subscriber/0,premium/1","message-type":"sub"}
Message: null - ChatMessage: Merci @TonyLar de t'être abonné !

*/