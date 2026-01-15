import {twitchBot} from "@modules/tmi/infrastructure/TmiBot";

const usernames = [
    'benoit_7354',
    'john_doe',
    'alice_smith',
    'twitch_user42',
    'chat_master99',
    'gamer_king',
    'bot_streamer',
    'streamer_jay',
    'user_tom',
    'nightbot',
    'admin_user',
    'funnyguy24',
    'twitch_lord',
    'pro_player123'
];

const getRandomUser = () => usernames[Math.floor(Math.random() * usernames.length)];

export const simulations: Record<string, (...args: never[]) => void> = {
    // Moderation
    // -----------------------------------------------------------------------------------------------------------------
    clearchat: () => {
        twitchBot.simulateEvent("clearchat", "vaince_woder");
    },
    ban: () => {
        const user = getRandomUser();
        twitchBot.simulateEvent(
            "ban",
            "vaince_woder",
            user,
            null,
            {
                "room-id": "90075649",
                "target-user-id": "1288819188",
                "tmi-sent-ts": Date.now().toString()
            },
            true,
        );
    },
    message: () => {
        twitchBot
            .sendMessage('MESSAGE')
            .then(() => {});
    },

    // Emotes
    // -----------------------------------------------------------------------------------------------------------------
    komodoHype: (username: string = 'vaince_woder') => {
        twitchBot.simulateEvent(
            "message",
            "vaince_woder",
            {
                "username": username,
                "message-type": "chat",
            },
            `KomodoHype`,
            true,
        );
    },
    wimjaGg: (username: string = 'vaince_woder') => {
        twitchBot.simulateEvent(
            "message",
            "vaince_woder",
            {
                "username": username,
                "message-type": "chat",
            },
            `wimjaGg`,
            true,
        );
    },
    wimjaKeur: (username: string = 'vaince_woder') => {
        twitchBot.simulateEvent(
            "message",
            "vaince_woder",
            {
                "username": username,
                "message-type": "chat",
            },
            `wimjaKeur`,
            true,
        );
    },
    troisPointsDeSuspension: (username: string = 'vaince_woder') => {
        twitchBot.simulateEvent(
            "message",
            "vaince_woder",
            {
                "username": username,
                "message-type": "chat",
            },
            `Honnêtement pas ouf...`,
            true,
        );
    },

    // Subscription
    // -----------------------------------------------------------------------------------------------------------------
    subscription: (isPrime: boolean = false) => {
        const user = getRandomUser();

        twitchBot.simulateEvent(
            "subscription",
            "vaince_woder",
            user,
            {
                "prime": isPrime,
                "plan": isPrime ? "Prime" : "1000",
                "planName": isPrime ? "Le peuple" : "L'élite"
            },
            "Allez, je m’abonne !",
            {
                "badge-info": {
                    "subscriber": "1"
                },
                "badges": {
                    "subscriber": "0",
                    "premium": "1"
                },
                "color": "#8A2BE2",
                "display-name": user,
                "emotes": null,
                "flags": null,
                "id": "5232ee44-54ab-47e5-989b-973820a55caa",
                "login": user,
                "mod": false,
                "msg-id": "sub",
                "msg-param-cumulative-months": true,
                "msg-param-months": false,
                "msg-param-multimonth-duration": true,
                "msg-param-multimonth-tenure": false,
                "msg-param-should-share-streak": false,
                "msg-param-sub-plan-name": isPrime ? "Le peuple" : "L'élite",
                "msg-param-sub-plan": isPrime ? "Prime" : "1000",
                "msg-param-was-gifted": "false",
                "room-id": "89872865",
                "subscriber": true,
                "system-msg": `${user} subscribed with Prime.`,
                "tmi-sent-ts": Date.now().toString(),
                "user-id": "471010942",
                "user-type": null,
                "vip": false,
                "emotes-raw": null,
                "badge-info-raw": "subscriber/1",
                "badges-raw": "subscriber/0,premium/1",
                "message-type": "sub"
            },
            true,
        );
    },
    resub: (isPrime: boolean = false) => {
        const user = getRandomUser();

        twitchBot.simulateEvent(
            "resub",
            "vaince_woder",
            user,
            12,
            "Toujours fidèle au poste !",
            {
                "badge-info": {
                    "subscriber": "1"
                },
                "badges": {
                    "subscriber": "0",
                    "premium": "1"
                },
                "color": "#8A2BE2",
                "display-name": user,
                "emotes": null,
                "flags": null,
                "id": "5232ee44-54ab-47e5-989b-973820a55caa",
                "login": user,
                "mod": false,
                "msg-id": "sub",
                "msg-param-cumulative-months": "8",
                "msg-param-months": false,
                "msg-param-multimonth-duration": true,
                "msg-param-multimonth-tenure": false,
                "msg-param-should-share-streak": false,
                "msg-param-sub-plan-name": isPrime ? "Le peuple" : "L'élite",
                "msg-param-sub-plan": isPrime ? "Prime" : "1000",
                "msg-param-was-gifted": "false",
                "room-id": "89872865",
                "subscriber": true,
                "system-msg": `${user} subscribed with Prime.`,
                "tmi-sent-ts": Date.now().toString(),
                "user-id": "471010942",
                "user-type": null,
                "vip": false,
                "emotes-raw": null,
                "badge-info-raw": "subscriber/1",
                "badges-raw": "subscriber/0,premium/1",
                "message-type": "sub"
            },
            {
                "prime": isPrime,
                "plan": isPrime ? "Prime" : "1000",
                "planName": isPrime ? "Le peuple" : "L'élite"
            },
            true,
        );
    },

    // Moderation
    // -----------------------------------------------------------------------------------------------------------------
    wzbotFollowMessage: () => {
        const user = getRandomUser();
        twitchBot.simulateEvent(
            "message",
            "vaince_woder",
            {
                "username": "wzbot",
                "message-type": "chat",
            },
            `👤 New Follower : ${user} (2 followers)`,
            true,
        );
    },

    // Others
    // -----------------------------------------------------------------------------------------------------------------
    cheer: () => {
        const user = getRandomUser();

        twitchBot.simulateEvent(
            "cheer",
            "vaince_woder",
            {
                "badge-info": null,
                "badges": {
                    "premium": "1"
                },
                "bits": "10",
                "color": "#0000FF",
                "display-name": user,
                "emotes": null,
                "first-msg": false,
                "flags": null,
                "id": "ebabb2b1-49cf-4ba9-afb1-52332ebd34af",
                "mod": false,
                "returning-chatter": false,
                "room-id": "68078157",
                "subscriber": false,
                "tmi-sent-ts": Date.now().toString(),
                "turbo": false,
                "user-id": "29833651",
                "user-type": null,
                "emotes-raw": null,
                "badge-info-raw": null,
                "badges-raw": "premium/1",
                "username": user,
                "message-type": "chat"
            },
            "Cheer10 Du floooouze",
            true,
        );
    },

    // WIP - Duel
    // -----------------------------------------------------------------------------------------------------------------
    duelMessage: (username: string = 'vaince_woder', target: string = 'manugraph') => {
        twitchBot.simulateEvent(
            "message",
            "vaince_woder",
            {
                "username": username,
                "message-type": "chat",
            },
            `!duel @${target}`,
            true,
        );
    },
    acceptDuelMessage: (username: string = 'vaince_woder') => {
        twitchBot.simulateEvent(
            "message",
            "vaince_woder",
            {
                "username": username,
                "message-type": "chat",
            },
            `!accept`,
            true,
        );
    },
};
