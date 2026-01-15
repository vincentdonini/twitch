import {ISpotifyService} from "@modules/spotify/application/ISpotifyService";
import {SpotifyTrack} from "@modules/spotify/application/SpotifyTrack";

export class GetCurrentlyPlaying {
    constructor(private service: ISpotifyService) {}

    async execute(): Promise<SpotifyTrack | null> {
        return await this.service.getCurrentlyPlaying();
    }
}
