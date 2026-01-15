import {Client, Events} from "tmi.js";
import {handleBan} from "@modules/tmi/application/handlers/banHandler";
import {handleClearChat} from "@modules/tmi/application/handlers/clearChatHandler";
import {handleMessage} from "@modules/tmi/application/handlers/messageHandler";
import {handleSubscription} from "@modules/tmi/application/handlers/subscriptionHandler";
import {handleResub} from "@modules/tmi/application/handlers/resubHandler";
import {handleCheer} from "@modules/tmi/application/handlers/cheerHandler";
import {handleSubGift} from "@modules/tmi/application/handlers/subGiftHandler";
import {handleAnonSubGift} from "@modules/tmi/application/handlers/anonSubGiftHandler";
import {handleRaided} from "@modules/tmi/application/handlers/raidedHandler";
import {handleHosted} from "@modules/tmi/application/handlers/hostedHandler";
import {handleHosting} from "@modules/tmi/application/handlers/hostingHandler";
import {handleConnected} from "@modules/tmi/application/handlers/connectedHandler";
import {handleReconnect} from "@modules/tmi/application/handlers/reconnectHandler";
import {handleDisconnected} from "@modules/tmi/application/handlers/disconnectedHandler";
import {TwitchApi} from "@modules/twitch/infrastructure/TwitchApi";

const TWITCH_CHANNEL_NAME = process.env.NEXT_PUBLIC_TWITCH_CHANNEL_NAME!;
const TWITCH_BOT_NAME = process.env.NEXT_PUBLIC_TWITCH_BOT_NAME!;

const channels = [TWITCH_CHANNEL_NAME];

class TwitchBot {
    private token: string;
    private client: Client;

    constructor() {
        this.token = this.getToken();
        this.client = this.createClient();
        this.initializeBot().then();
    }

    private getToken(): string {
        if (typeof window !== "undefined") {
            return localStorage.getItem("twitch_token")!;
        } else {
            return '';
        }
    }

    private createClient(): Client {
        return new Client({
            options: {debug: false},
            identity: {
                username: TWITCH_BOT_NAME,
                password: `oauth:${this.token}`,
            },
            channels: channels,
        });
    }

    private async initializeBot() {
        const twitchApi = new TwitchApi(this.token);
        this.token = await twitchApi.getValidToken();

        // Crée une instance du client tmi avec le token valide
        this.client = new Client({
            options: {debug: false},
            identity: {
                username: TWITCH_BOT_NAME,
                password: `oauth:${this.token}`,
            },
            channels: channels,
        });

        this.setupListeners();
        await this.client.connect().catch(console.error);
    }

    private setupListeners() {

        // ACTIONS
        // -------------------------------------------------------------------------------------------------------------
        this.client.on('ban', handleBan.bind(this));
        this.client.on("clearchat", handleClearChat.bind(this));

        // MESSAGES
        // -------------------------------------------------------------------------------------------------------------
        this.client.on('message', handleMessage.bind(this));

        // SUBSCRIPTIONS EVENTS
        // -------------------------------------------------------------------------------------------------------------
        this.client.on("subscription", handleSubscription.bind(this));
        this.client.on("resub", handleResub.bind(this));

        // CHEER EVENTS
        // -------------------------------------------------------------------------------------------------------------
        this.client.on('cheer', handleCheer.bind(this));

        // GIFT EVENTS
        // -------------------------------------------------------------------------------------------------------------
        this.client.on('subgift', handleSubGift.bind(this));
        this.client.on('anonsubgift', handleAnonSubGift.bind(this));
        // this.client.on('giftpaidupgrade', handleGiftPaidUpgrade.bind(this));
        // this.client.on('anongiftpaidupgrade', handleAnonGiftPaidUpgrade.bind(this));

        // RAID EVENT
        // -------------------------------------------------------------------------------------------------------------
        this.client.on('raided', handleRaided.bind(this));

        // HOSTING EVENTS
        // -------------------------------------------------------------------------------------------------------------
        this.client.on('hosted', handleHosted.bind(this));
        this.client.on('hosting', handleHosting.bind(this));


        // CONNECTION EVENTS
        // -------------------------------------------------------------------------------------------------------------
        this.client.on('disconnected', handleDisconnected.bind(this));
        this.client.on('connected', handleConnected.bind(this));
        this.client.on('reconnect', handleReconnect.bind(this));
    }

    public async sendMessage(message: string, mute: boolean = false): Promise<void> {
        if (mute) return;

        await this.client.say(TWITCH_BOT_NAME, message);
    }

    public async deleteMessage(uuid: string) {
        console.log('deleteMessage', TWITCH_CHANNEL_NAME, uuid);
        await this.client.deletemessage(TWITCH_CHANNEL_NAME, uuid).then((data) => {
            console.log('THEN', data);
        }).catch((err) => {
            console.log('ERR', err);
        });

        // const channel = `${TWITCH_CHANNEL_NAME}`;
        // console.log('Tentative suppression de message :', channel, uuid);
        //
        // try {
        //     await this.client.deletemessage(channel, uuid);
        //     console.log('✅ Message supprimé avec succès');
        // } catch (error) {
        //     console.error('❌ Erreur lors de la suppression du message :', error);
        // }
    }

    public async connect() {
        this.client.connect().catch(console.error);
    }

    public simulateEvent(eventType: keyof Events, ...params: unknown[]) {
        if (this.client.listenerCount(eventType) > 0) {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-expect-error
            this.client.emit(eventType, ...params);
        } else {
            console.error(`Event type "${eventType}" does not have any listeners.`);
        }
    }
}

export const twitchBot = new TwitchBot();
