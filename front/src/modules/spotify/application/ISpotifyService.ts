import {SpotifyTrack} from "@modules/spotify/application/SpotifyTrack";

export interface ISpotifyService {
    getCurrentlyPlaying(): Promise<SpotifyTrack | null>;
}
