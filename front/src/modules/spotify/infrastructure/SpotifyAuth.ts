import axios from "axios";

const OAUTH_URL = process.env.NEXT_PUBLIC_SPOTIFY_OAUTH_URL!;
const CLIENT_ID = process.env.NEXT_PUBLIC_SPOTIFY_CLIENT_ID!;
const CLIENT_SECRET = process.env.NEXT_PUBLIC_SPOTIFY_CLIENT_SECRET!;
const SCOPE = process.env.NEXT_PUBLIC_SPOTIFY_SCOPE!;
const REDIRECT_URI = process.env.NEXT_PUBLIC_SPOTIFY_REDIRECT_URI!;

export class SpotifyAuth {
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
            `${OAUTH_URL}/api/token`,
            new URLSearchParams({
                code,
                grant_type: 'authorization_code',
                redirect_uri: REDIRECT_URI,
            }),
            {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': 'Basic ' + Buffer.from(CLIENT_ID + ':' + CLIENT_SECRET).toString('base64'),
                },
            }
        );

        return response.data;
    }
}