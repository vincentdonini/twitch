import {TwitchAuth} from "@modules/twitch/infrastructure/TwitchAuth";

export async function authenticateUser(code: string) {
    const tokenData = await TwitchAuth.exchangeCodeForToken(code);
    if (!tokenData.access_token) {
        throw new Error("Authentication failed...");
    }

    localStorage.setItem("twitch_token", tokenData.access_token);
    localStorage.setItem("twitch_refresh_token", tokenData.refresh_token);
    localStorage.setItem("twitch_token_expiry", tokenData.expires_in);
    localStorage.setItem("twitch_scope", tokenData.scope);
    return tokenData.access_token;
}
