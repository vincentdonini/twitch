import axios from "axios";

const OAUTH_URL = process.env.NEXT_PUBLIC_TWITCH_OAUTH_URL!;
const CLIENT_ID = process.env.NEXT_PUBLIC_TWITCH_CLIENT_ID!;
const CLIENT_SECRET = process.env.NEXT_PUBLIC_TWITCH_CLIENT_SECRET!;
const SCOPE = process.env.NEXT_PUBLIC_TWITCH_SCOPE!;
const REDIRECT_URI = process.env.NEXT_PUBLIC_TWITCH_REDIRECT_URI!;

export class TwitchAuth {
    static getLoginUrl() {
        const params = new URLSearchParams({
            client_id: CLIENT_ID,
            response_type: "code",
            redirect_uri: REDIRECT_URI,
            scope: SCOPE,
        });
        return `${OAUTH_URL}/authorize?${params.toString()}`;
    }

    static async exchangeCodeForToken(code: string) {
        const response = await axios.post(
            `${OAUTH_URL}/token`,
            null,
            {
                params: {
                    client_id: CLIENT_ID,
                    client_secret: CLIENT_SECRET,
                    code,
                    grant_type: "authorization_code",
                    redirect_uri: REDIRECT_URI,
                },
            }
        );

        return response.data;
    }
}
