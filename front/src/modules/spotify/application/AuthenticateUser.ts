import {SpotifyAuth} from "@modules/spotify/infrastructure/SpotifyAuth";

export async function authenticateUser(code: string) {
    const tokenData = await SpotifyAuth.exchangeCodeForToken(code);
    if (!tokenData.access_token) {
        throw new Error("Authentication failed...");
    }

    localStorage.setItem("spotify_token", tokenData.access_token);
    localStorage.setItem("spotify_refresh_token", tokenData.refresh_token);
    localStorage.setItem("spotify_token_expiry", tokenData.expires_in);

    return tokenData.access_token;
}
