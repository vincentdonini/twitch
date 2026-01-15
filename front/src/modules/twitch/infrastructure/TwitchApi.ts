import axios from "axios";
import {ITwitchService} from "@modules/twitch/application/ITwitchService";
import {UserIdentifier} from "@common/api/domain/UserIdentifier";

const OAUTH_URL = process.env.NEXT_PUBLIC_TWITCH_OAUTH_URL!;
const API_URL = process.env.NEXT_PUBLIC_TWITCH_API_URL!;
const CLIENT_ID = process.env.NEXT_PUBLIC_TWITCH_CLIENT_ID!;
const CLIENT_SECRET = process.env.NEXT_PUBLIC_TWITCH_CLIENT_SECRET!;

export class TwitchApi implements ITwitchService {
    constructor(private token: string) {
    }

    private async validateToken(): Promise<boolean> {
        try {
            const response = await axios.get(`${OAUTH_URL}/validate`, {
                headers: {
                    Authorization: `OAuth ${this.token}`,
                },
            });

            // Si la réponse est un succès (status 200), cela signifie que le token est valide
            return response.status === 200;
        } catch (error) {
            // Si une erreur se produit (par exemple, token invalide), retourne false
            console.error("Erreur de validation du token", error);
            return false;
        }
    }

    private async refreshToken(): Promise<string> {
        const refreshToken = localStorage.getItem('twitch_refresh_token');
        if (!refreshToken) throw new Error('No Twitch refresh token available');
        if (!CLIENT_ID) throw new Error('Missing TWITCH_CLIENT_ID');

        const params = new URLSearchParams();
        params.append('grant_type', 'refresh_token');
        params.append('refresh_token', refreshToken);
        params.append('client_id', CLIENT_ID);
        params.append('client_secret', CLIENT_SECRET);

        try {
            const response = await axios.post(`${OAUTH_URL}/token`, params, {
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            });

            const {access_token, refresh_token: newRefreshToken, expires_in} = response.data;

            this.token = access_token;
            localStorage.setItem("twitch_token", access_token);
            if (newRefreshToken) {
                localStorage.setItem("twitch_refresh_token", newRefreshToken);
            }
            if (expires_in) {
                localStorage.setItem("twitch_token_expiry", expires_in);
            }

            return access_token;
        } catch (err) {
            console.error("Error refreshing Twitch token", err);
            throw new Error("Error refreshing Twitch token");
        }
    }

    private async makeRequest<T>(url: string): Promise<T> {
        const attempt = async (token: string): Promise<T> => {
            const res = await fetch(url, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    'Client-Id': CLIENT_ID,
                },
            });

            if (res.status === 401) {
                const body = await res.json();
                if (body.message === "Unauthorized") {
                    throw new Error("expired_token");
                }
            }

            if (!res.ok) throw new Error(`Twitch API error: ${res.status}`);
            return await res.json();
        };

        try {
            return await attempt(this.token);
        } catch (err: any) {
            if (err.message === "expired_token") {
                const newToken = await this.refreshToken();
                return await attempt(newToken);
            }
            throw err;
        }
    }

    // Méthode publique pour s'assurer que le token est valide et le rafraîchir si nécessaire
    public async getValidToken(): Promise<string> {
        const isValid = await this.validateToken();
        console.log('getValidToken', isValid);
        if (!isValid) {
            console.log("Le token est invalide, rafraîchissement du token...");
            return await this.refreshToken();
        }
        return this.token; // Retourne le token s'il est valide
    }

    async getDonors(): Promise<UserIdentifier[]> {
        const url = `${API_URL}/bits/leaderboard`;
        const response = await this.makeRequest<any>(url);

        if (!response.data) return [];

        return response.data.map((follower: any) => this.mapToTwitchUser(follower));
    }

    async getTopDonor(): Promise<UserIdentifier> {
        const url = `${API_URL}/bits/leaderboard`;
        const response = await this.makeRequest<any>(url);
        const follower = response.data?.[0];
        return this.mapToTwitchUser(follower);
    }

    async getFollowers(): Promise<UserIdentifier[]> {
        const url = `${API_URL}/channels/followers?broadcaster_id=131666619&first=100`;
        const response = await this.makeRequest<any>(url);

        if (!response.data) return [];

        return response.data.map((follower: any) => this.mapToTwitchUser(follower));
    }

    async getLastFollower(): Promise<UserIdentifier> {
        const url = `${API_URL}/channels/followers?broadcaster_id=131666619&first=1`;
        const response = await this.makeRequest<any>(url);
        const follower = response.data?.[0];
        return this.mapToTwitchUser(follower);
    }

    async getSubscribers(): Promise<UserIdentifier[]> {
        const url = `${API_URL}/subscriptions?broadcaster_id=131666619&first=100`;
        const response = await this.makeRequest<any>(url);

        if (!response.data) return [];

        return response.data.map((follower: any) => this.mapToTwitchUser(follower));
    }

    async getLastSubscriber(): Promise<UserIdentifier> {
        const url = `${API_URL}/subscriptions?broadcaster_id=131666619&first=1`;
        const response = await this.makeRequest<any>(url);

        const subscriber = response.data?.[0];
        return this.mapToTwitchUser(subscriber);
    }

    private mapToTwitchUser(raw: any): UserIdentifier {
        return {
            id: raw.user_id,
            username: raw.user_name,
        };
    }
}
