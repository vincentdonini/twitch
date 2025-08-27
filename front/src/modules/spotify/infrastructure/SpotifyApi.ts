import axios from "axios";
import {ISpotifyService} from "@modules/spotify/application/ISpotifyService";
import {SpotifyTrack} from "@modules/spotify/application/SpotifyTrack";

const OAUTH_URL = process.env.NEXT_PUBLIC_SPOTIFY_OAUTH_URL!;
const API_URL = process.env.NEXT_PUBLIC_SPOTIFY_API_URL!;
const CLIENT_ID = process.env.NEXT_PUBLIC_SPOTIFY_CLIENT_ID!;
const CLIENT_SECRET = process.env.NEXT_PUBLIC_SPOTIFY_CLIENT_SECRET!;

export class SpotifyApi implements ISpotifyService {
    constructor(private token: string) {}

    private async refreshToken(): Promise<string> {
        const refreshToken = localStorage.getItem('spotify_refresh_token');
        if (!refreshToken) throw new Error('No refresh token available...');
        if (!CLIENT_ID) throw new Error('Missing CLIENT_ID');

        const headers = {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Authorization': 'Basic ' + Buffer.from(`${CLIENT_ID}:${CLIENT_SECRET}`).toString('base64'),
        };

        const params = new URLSearchParams();
        params.append('grant_type', 'refresh_token');
        params.append('refresh_token', refreshToken);
        params.append('client_id', CLIENT_ID);

        try {
            const response = await axios.post(
                `${OAUTH_URL}/api/token`,
                params,
                {headers},
            );
            const {access_token, refresh_token, expires_in} = response.data;

            this.token = access_token;
            localStorage.setItem("spotify_token", access_token);

            if (refresh_token) {
                localStorage.setItem("spotify_refresh_token", refresh_token);
            }

            if (expires_in) {
                localStorage.setItem("spotify_token_expiry", expires_in);
            }
            return access_token;
        } catch (error) {
            console.error("Error refreshing token", error);
            throw new Error("Error refreshing token");
        }
    }

    async getCurrentlyPlaying(): Promise<SpotifyTrack | null> {
        const makeRequest = async (token: string) => {
            const res = await fetch(`${API_URL}/v1/me/player/currently-playing`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });

            if (res.status === 204 || res.status === 205) return null;
            if (!res.ok) {
                const errorBody = await res.json();
                if (res.status === 401 && errorBody.error?.message === "The access token expired") {
                    throw new Error("expired_token");
                }
                throw new Error("Spotify error");
            }

            const data = await res.json();

            const releaseDate = new Date(data.item.album.release_date);

            return {
                id: data.item.id,
                name: data.item.name,
                artist: data.item.artists.map((a: any) => a.name).join(", "),
                album: data.item.album.name,
                year: releaseDate.getFullYear(),
                image: data.item.album.images[0].url,
                href: data.item.href,
                url: data.item.external_urls.spotify,
                isPlaying: data.is_playing,
            };
        };

        try {
            return await makeRequest(this.token);
        } catch (error: any) {
            if (error.message === "expired_token") {
                const newToken = await this.refreshToken();
                return await makeRequest(newToken);
            }
            throw error;
        }
    }
}
