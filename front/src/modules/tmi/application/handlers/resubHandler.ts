import {SubMethods, SubUserstate} from "tmi.js";
import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";
import {queueAlert} from "@modules/tmi/application/commands/queueAlert";
import {store} from "@stores/stores";
import {setLastSubscription} from "@modules/tmi/application/store/engagementSlice";
import {postEvent} from "@common/api/application/PostEvent";

const debug = true;
const mutBot = false;

const classicMessages = [
    "Bienvenue @{username} dans la team des abos ! {months} mois de soutien déjà 💜.",
    "@{username} s’abonne pour {months} mois de pur bonheur. Profite bien 😎.",
    "Un gros GG à @{username} pour son {months}ème mois d'abonnement 🔥.",
    "@{username}, t’as rejoint le club des abos depuis {months} mois déjà... Bienvenue dans le cercle ✨!",
    "Oh oui ! @{username} démarre son {months}ème mois d’abonnement et ça régale 🙌.",
    "Un nouveau sub tout neuf pour @{username}. Merci pour tes {months} mois de soutien !",
    "Merci @{username} ! Tes {months} mois d'abonnement illumine ce stream 🌟.",
    "@{username}, t’as signé pour un {months}ème mois de bonheur. Merci 💜.",
    "@{username} a cliqué sur 'S’abonner' {months} fois ? Vraiment...",
    "@{username} rejoint les rangs des élus avec {months} mois d'abonnement 👀.",
    "{months} mois d'abonnement, et hop, @{username} rejoint le top!",
    "@{username} s'est abonné {months} fois à la chaine... Erreur ou pas, la maison ne rembourse pas. Bisous 😘.",
    "Mon chat commencait à me harceler... Merci @{username}, grâce à ton {months}ème mois d'abonnement je peux enfin lui acheter ses croquettes 🐱.",
    "{months} mois de force donné par @{username}. Tu es officiellement notre GOAT.",
    "@{username} a dit OUI au sub depuis {months} mois, et ça mérite du respect ✊.",
    "@{username} a sorti la CB pour son {months}ème mois de soutien. Merci !",
    "@{username} renouvelle son abonnement ! Un {months}ème mois solide comme mes biceps 💪",
    "@{username} a signé pour un {months}ème mois ! T'es officiellement un pilier du stream.",
    "@{username}, t’es là depuis {months} mois déjà ! Ah ouai quand même...",
    "Un resub de @{username}, ça arrive toujours au bon moment 😍.",
];
const primeMessages = [
    "@{username} a lâché son Prime ce mois-ci... {months} mois déjà, c'est beau. Merci !",
    "@{username} a réveillé son Prime pour son {months}ème mois. Un mois de plus sans pub, ça valait le coup 😏",
    "@{username} a bien utilisé son Prime pour son {months}ème mois, comme une personne de goût ✨",
    "@{username} a transformé un Prime oublié en un {months}ème mois de soutien 💪",
    "Ce mois-ci, le Prime de @{username} a trouvé son vrai foyer : ce stream 💜",
    "T’as retrouvé ton Prime au fond d'un tiroir ce mois-ci, hein @{username} ? Bien joué 😎",
    "Merci @{username} ! Ce mois, ton Prime évite la poussière. Merci pour tes {months} mois de soutien 💪.",
    "Ce mois-ci, @{username} a dit : \"Pas question que ce Prime dorme\". Et on valide pour un {months}ème mois !",
    "Merci @{username} ! Ton Prime va passer un mois au paradis du contenu (modeste).",
    "Ce mois-ci, @{username} a décidé de faire fructifier son prime sur notre chaine.",
    "Un Prime chez nous pour {months} mois, c’est un vrai soutien. Et tu sais quoi ? On kiffe ça 😁",
    "Le Prime de @{username} a enfin servi à autre chose qu’à rater des colis. Merci 😄.",
    "@{username} a prouvé que même un Prime peut faire du bien avec un {months}ème mois 😅",
    "Bienvenue à @{username}, membre de la Société Protectrice des Primes (SPP) ! Merci d’avoir sauvé ton Prime de la poubelle 🛑💸",
    "Vous aussi faites comme @{username}, pour recycler votre prime cliquer sur le bouton \"S’abonner gratuitement\"",
];

export function handleResub(
    channel: string,
    username: string,
    months: number,
    message: string,
    userstate: SubUserstate,
    methods: SubMethods
) {
    const isPrime = methods["prime"] === true;
    const cumulativeMonths = userstate["msg-param-cumulative-months"] as string;

    const messages = isPrime ? primeMessages : classicMessages;
    const template = messages[Math.floor(Math.random() * messages.length)];

    const chatMessage = template
        .replace(/\{username\}/g, username)
        .replace(/\{months\}/g, cumulativeMonths);

    if (debug) {
        console.log(`[handleResub]`, {
            channel,
            username,
            months,
            message,
            userstate,
            methods,
            chatMessage,
        });
    }

    queueAlert(store.dispatch, {
        type: "resub",
        username: username,
        message: `est abonné depuis ${cumulativeMonths} mois`,
        timestamp: Date.now(),
    });

    store.dispatch(setLastSubscription(username));
    postEvent('resub', userstate['user-id']!, username).then();

    if (!mutBot) {
        twitchBot
            .sendMessage(chatMessage)
            .then(() => {});
    }
}

/*

[handleResub]
Username: skyse_k
SubMethods: {"prime":true,"plan":"Prime","planName":"Les gens gentils qui supportent Laink"}
SubUserstate: {"badge-info":{"subscriber":"8"},"badges":{"subscriber":"6","zevent-2024":"1"},"color":"#0000FF","display-name":"skyse_k","emotes":null,"flags":null,"id":"23312dcf-deae-41e5-a944-cb2831907420","login":"skyse_k","mod":false,"msg-id":"resub","msg-param-cumulative-months":"8","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Les gens gentils qui supportent Laink","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"89872865","subscriber":true,"system-msg":"skyse_k subscribed with Prime. They've subscribed for 8 months!","tmi-sent-ts":"1743617101400","user-id":"128051342","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/8","badges-raw":"subscriber/6,zevent-2024/1","message-type":"resub"}
Message: null
ChatMessage: Merci @skyse_k pour tes 8 mois de sub!

[handleResub]
Username: Totonain
SubMethods: {"prime":false,"plan":"1000","planName":"L'élite du live"}
SubUserstate: {"badge-info":{"subscriber":"8"},"badges":{"subscriber":"6","sub-gifter":"1"},"color":"#9ACD32","display-name":"Totonain","emotes":null,"flags":null,"id":"2f0e6401-1cf9-4bfc-a82d-c36877e73df8","login":"totonain","mod":false,"msg-id":"resub","msg-param-cumulative-months":"8","msg-param-months":false,"msg-param-multimonth-duration":"6","msg-param-multimonth-tenure":"2","msg-param-should-share-streak":true,"msg-param-streak-months":"4","msg-param-sub-plan-name":"L'élite du live","msg-param-sub-plan":"1000","msg-param-was-gifted":"false","room-id":"89873316","subscriber":true,"system-msg":"Totonain subscribed at Tier 1. They've subscribed for 8 months, currently on a 4 month streak!","tmi-sent-ts":"1743617125216","user-id":"161625616","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/8","badges-raw":"subscriber/6,sub-gifter/1","message-type":"resub"}
Message: null
ChatMessage: Merci @Totonain pour tes 8 mois de sub!

[handleResub]
Username: eloctopoulpe
SubMethods: {"prime":true,"plan":"Prime","planName":"Le troupeau de bananes"}
SubUserstate: {"badge-info":{"subscriber":"51"},"badges":{"subscriber":"48","premium":"1"},"color":"#1E90FF","display-name":"eloctopoulpe","emotes":null,"flags":null,"id":"eb6b9bcd-c638-4ab4-b404-b905ea4e080b","login":"eloctopoulpe","mod":false,"msg-id":"resub","msg-param-cumulative-months":"51","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Le troupeau de bananes","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"31289086","subscriber":true,"system-msg":"eloctopoulpe subscribed with Prime. They've subscribed for 51 months!","tmi-sent-ts":"1743617134475","user-id":"411773765","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/51","badges-raw":"subscriber/48,premium/1","message-type":"resub"}
Message: null
ChatMessage: Merci @eloctopoulpe pour tes 51 mois de sub!

[handleResub]
Username: sofian_arb
SubMethods: {"prime":true,"plan":"Prime","planName":"L'élite du live"}
SubUserstate: {"badge-info":{"subscriber":"15"},"badges":{"subscriber":"12","premium":"1"},"color":null,"display-name":"sofian_arb","emotes":null,"flags":null,"id":"bd273bc5-baaa-4364-a067-64e0d8f66a86","login":"sofian_arb","mod":false,"msg-id":"resub","msg-param-cumulative-months":"15","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"L'élite du live","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"89873316","subscriber":true,"system-msg":"sofian_arb subscribed with Prime. They've subscribed for 15 months!","tmi-sent-ts":"1743617137045","user-id":"804828332","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/15","badges-raw":"subscriber/12,premium/1","message-type":"resub"}
Message: null
ChatMessage: Merci @sofian_arb pour tes 15 mois de sub!

[handleResub]
Username: Al_Pacini
SubMethods: {"prime":true,"plan":"Prime","planName":"Le troupeau de bananes"}
SubUserstate: {"badge-info":{"subscriber":"20"},"badges":{"subscriber":"12","premium":"1"},"color":"#DAA520","display-name":"Al_Pacini","emotes":null,"flags":null,"id":"ad281fd9-b87d-41fd-bea8-51e96d15b3e0","login":"al_pacini","mod":false,"msg-id":"resub","msg-param-cumulative-months":"20","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Le troupeau de bananes","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"31289086","subscriber":true,"system-msg":"Al_Pacini subscribed with Prime. They've subscribed for 20 months!","tmi-sent-ts":"1743617166850","user-id":"84798210","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/20","badges-raw":"subscriber/12,premium/1","message-type":"resub"}
Message: null
ChatMessage: Merci @Al_Pacini pour tes 20 mois de sub!

[handleResub]
Username: Viinception
SubMethods: {"prime":true,"plan":"Prime","planName":"Le troupeau de bananes"}
SubUserstate: {"badge-info":{"subscriber":"61"},"badges":{"subscriber":"60","premium":"1"},"color":"#8A2BE2","display-name":"Viinception","emotes":null,"flags":null,"id":"c0f7b6cb-4456-4392-a3ed-74319000d7e8","login":"viinception","mod":false,"msg-id":"resub","msg-param-cumulative-months":"61","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":true,"msg-param-streak-months":"61","msg-param-sub-plan-name":"Le troupeau de bananes","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"31289086","subscriber":true,"system-msg":"Viinception subscribed with Prime. They've subscribed for 61 months, currently on a 61 month streak!","tmi-sent-ts":"1743617169423","user-id":"71666191","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/61","badges-raw":"subscriber/60,premium/1","message-type":"resub"}
Message: null
ChatMessage: Merci @Viinception pour tes 61 mois de sub!

[handleResub]
Username: benbohrok
SubMethods: {"prime":false,"plan":"1000","planName":"L'élite du live"}
SubUserstate: {"badge-info":{"subscriber":"58"},"badges":{"subscriber":"48","zevent-2024":"1"},"color":"#FF7F50","display-name":"benbohrok","emotes":null,"flags":null,"id":"db318480-3b4a-4f43-9ddf-b7cb8302bc94","login":"benbohrok","mod":false,"msg-id":"resub","msg-param-cumulative-months":"58","msg-param-months":false,"msg-param-multimonth-duration":"40","msg-param-multimonth-tenure":"39","msg-param-should-share-streak":false,"msg-param-sub-plan-name":"L'élite du live","msg-param-sub-plan":"1000","msg-param-was-gifted":"false","room-id":"89873316","subscriber":true,"system-msg":"benbohrok subscribed at Tier 1. They've subscribed for 58 months!","tmi-sent-ts":"1743617287807","user-id":"60525180","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/58","badges-raw":"subscriber/48,zevent-2024/1","message-type":"resub"}
Message: null
ChatMessage: Merci @benbohrok pour tes 58 mois de sub!

[handleResub]
Username: MGK_ESPORT
SubMethods: {"prime":true,"plan":"Prime","planName":"Le troupeau de bananes"}
SubUserstate: {"badge-info":{"subscriber":"2"},"badges":{"subscriber":"0","premium":"1"},"color":null,"display-name":"MGK_ESPORT","emotes":null,"flags":null,"id":"cf519d51-bcd9-4e7d-a547-81abcf7a5f00","login":"mgk_esport","mod":false,"msg-id":"resub","msg-param-cumulative-months":"2","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":true,"msg-param-streak-months":"2","msg-param-sub-plan-name":"Le troupeau de bananes","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"31289086","subscriber":true,"system-msg":"MGK_ESPORT subscribed with Prime. They've subscribed for 2 months, currently on a 2 month streak!","tmi-sent-ts":"1743617290886","user-id":"1089437646","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/2","badges-raw":"subscriber/0,premium/1","message-type":"resub"}
Message: YYYEEAAAHHH
ChatMessage: Merci @MGK_ESPORT pour tes 2 mois de sub!

[handleResub]
Username: Thithiinsidesgames
SubMethods: {"prime":false,"plan":"1000","planName":"Le troupeau de bananes"}
SubUserstate: {"badge-info":{"subscriber":"61"},"badges":{"subscriber":"60"},"color":"#B22222","display-name":"Thithiinsidesgames","emotes":null,"flags":null,"id":"6b403719-7cf6-4306-8cff-89d9752bba82","login":"thithiinsidesgames","mod":false,"msg-id":"resub","msg-param-cumulative-months":"61","msg-param-months":false,"msg-param-multimonth-duration":"45","msg-param-multimonth-tenure":"44","msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Le troupeau de bananes","msg-param-sub-plan":"1000","msg-param-was-gifted":"false","room-id":"31289086","subscriber":true,"system-msg":"Thithiinsidesgames subscribed at Tier 1. They've subscribed for 61 months!","tmi-sent-ts":"1743617312886","user-id":"94726090","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/61","badges-raw":"subscriber/60","message-type":"resub"}
Message: null
ChatMessage: Merci @Thithiinsidesgames pour tes 61 mois de sub!

[handleResub]
Username: Thegau
SubMethods: {"prime":false,"plan":"1000","planName":"Les gens gentils qui supportent Laink"}
SubUserstate: {"badge-info":{"subscriber":"8"},"badges":{"subscriber":"6","premium":"1"},"color":"#0000FF","display-name":"Thegau","emotes":null,"flags":null,"id":"ac66ec8e-8b31-4adf-8fe7-dc6053c57723","login":"thegau","mod":false,"msg-id":"resub","msg-param-cumulative-months":"8","msg-param-months":false,"msg-param-multimonth-duration":"7","msg-param-multimonth-tenure":"6","msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Les gens gentils qui supportent Laink","msg-param-sub-plan":"1000","msg-param-was-gifted":"false","room-id":"89872865","subscriber":true,"system-msg":"Thegau subscribed at Tier 1. They've subscribed for 8 months!","tmi-sent-ts":"1743617317883","user-id":"164888805","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/8","badges-raw":"subscriber/6,premium/1","message-type":"resub"}
Message: null
ChatMessage: Merci @Thegau pour tes 8 mois de sub!

[handleResub]
Username: minizza59380
SubMethods: {"prime":false,"plan":"1000","planName":"Random péon "}
SubUserstate: {"badge-info":{"subscriber":"24"},"badges":{"subscriber":"24","zevent-2024":"1"},"color":"#DAA520","display-name":"minizza59380","emotes":null,"flags":null,"id":"ee8ca406-fb7e-47b5-9be5-e64d207dc8f3","login":"minizza59380","mod":false,"msg-id":"resub","msg-param-cumulative-months":"24","msg-param-months":false,"msg-param-multimonth-duration":"24","msg-param-multimonth-tenure":"23","msg-param-should-share-streak":false,"msg-param-sub-plan-name":"Random péon ","msg-param-sub-plan":"1000","msg-param-was-gifted":"false","room-id":"68078157","subscriber":true,"system-msg":"minizza59380 subscribed at Tier 1. They've subscribed for 24 months!","tmi-sent-ts":"1743617376363","user-id":"216019451","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/24","badges-raw":"subscriber/24,zevent-2024/1","message-type":"resub"}
Message: null
ChatMessage: Merci @minizza59380 pour tes 24 mois de sub!

[handleResub]
Username: malroth24
SubMethods: {"prime":false,"plan":"1000","planName":"L'élite du live"}
SubUserstate: {"badge-info":{"subscriber":"3"},"badges":{"subscriber":"3"},"color":null,"display-name":"malroth24","emotes":null,"flags":null,"id":"18d91f4a-a768-4fad-88bc-b6ea2a51fe48","login":"malroth24","mod":false,"msg-id":"resub","msg-param-cumulative-months":"3","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"L'élite du live","msg-param-sub-plan":"1000","msg-param-was-gifted":"false","room-id":"89873316","subscriber":true,"system-msg":"malroth24 subscribed at Tier 1. They've subscribed for 3 months!","tmi-sent-ts":"1743617392028","user-id":"1129158100","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/3","badges-raw":"subscriber/3","message-type":"resub"}
Message: null
ChatMessage: Merci @malroth24 pour tes 3 mois de sub!

[handleResub]
Username: Delfyr_
SubMethods: {"prime":true,"plan":"Prime","planName":"L'élite du live"}
SubUserstate: {"badge-info":{"subscriber":"10"},"badges":{"subscriber":"6","premium":"1"},"color":"#8A2BE2","display-name":"Delfyr_","emotes":null,"flags":null,"id":"8d550504-107a-47a2-804b-98380ec78882","login":"delfyr_","mod":false,"msg-id":"resub","msg-param-cumulative-months":"10","msg-param-months":false,"msg-param-multimonth-duration":true,"msg-param-multimonth-tenure":false,"msg-param-should-share-streak":false,"msg-param-sub-plan-name":"L'élite du live","msg-param-sub-plan":"Prime","msg-param-was-gifted":"false","room-id":"89873316","subscriber":true,"system-msg":"Delfyr_ subscribed with Prime. They've subscribed for 10 months!","tmi-sent-ts":"1743617402270","user-id":"236604064","user-type":null,"vip":false,"emotes-raw":null,"badge-info-raw":"subscriber/10","badges-raw":"subscriber/6,premium/1","message-type":"resub"} - Message: null - ChatMessage: Merci @Delfyr_ pour tes 10 mois de sub!

 */
