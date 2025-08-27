import * as React from 'react';
import {useEffect, useState} from "react";
import {SpotifyTrack} from "@modules/spotify/application/SpotifyTrack";
import {SpotifyApi} from "@modules/spotify/infrastructure/SpotifyApi";
import {GetCurrentlyPlaying} from "@modules/spotify/domain/GetCurrentlyPlaying";
import Box from '@mui/material/Box';
import IconButton from '@mui/material/IconButton';
import PauseRounded from '@mui/icons-material/PauseRounded';
import VolumeOffIcon from '@mui/icons-material/VolumeOff';
import {store} from "@stores/stores";
import {clearActiveMusic, setActiveMusic} from "@stores/spotifySlice";

export default function SpotifyNowPlayingCard() {

    const [track, setTrack] = useState<SpotifyTrack | null>(null);
    const [isPaused, setIsPaused] = useState<boolean>(true);

    useEffect(() => {
        const token = localStorage.getItem("spotify_token");
        if (!token) return;

        const api = new SpotifyApi(token);
        const getTrack = new GetCurrentlyPlaying(api);

        const fetchTrack = async () => {
            try {
                const data = await getTrack.execute();

                if (data) {
                    setIsPaused(!data.isPlaying);

                    store.dispatch(setActiveMusic({
                        artist: data.artist,
                        music: data.name,
                        album: data.album,
                        year: data.year,
                        url: data.url,
                    }));

                    setTrack((prevTrack) => {
                        const newKey = data.name + data.artist;
                        const prevKey = prevTrack ? prevTrack.name + prevTrack.artist : null;

                        if (newKey !== prevKey) {
                            return data;
                        }
                        return prevTrack;
                    });
                } else {
                    store.dispatch(clearActiveMusic());
                    setTrack(null);
                    setIsPaused(false);
                }

            } catch (err) {
                console.error("Erreur lors du fetch du morceau en cours", err);
                setTrack(null);
                setIsPaused(true);
            }
        };
        fetchTrack().then();
        const intervalId = setInterval(fetchTrack, 5000);

        return () => clearInterval(intervalId);
    }, []);

    return (
        <Box className="flex flex-col items-end" sx={{position: 'absolute', bottom: 35, right: 360}}>
            {
                track === null ? (
                    <>
                        <Box className="fade-in p-2 bg-black/60 rounded-xl shadow-md text-white overflow-hidden">
                            <IconButton sx={{color: '#fff'}}>
                                <VolumeOffIcon fontSize="medium"/>
                            </IconButton>
                            {/*No music*/}
                        </Box>
                    </>
                ) : (
                    <>
                        <div className="fade-in w-[270px] p-4 bg-black/60 rounded-xl shadow-md text-white overflow-hidden">
                            <div className="flex items-center">
                                <div className="relative w-[70px] h-[70px] rounded-lg overflow-hidden flex-shrink-0">
                                    <img
                                        alt={track.name}
                                        src={track.image}
                                        className={`w-full h-full object-cover rounded-lg transition duration-300 ease-in-out ${isPaused ? 'grayscale-[60%]' : ''}`}
                                    />

                                    <div
                                        className={`absolute top-0 left-0 w-full h-full bg-black/40 rounded-lg transition-opacity duration-300 pointer-events-none ${isPaused ? 'opacity-100' : 'opacity-0'}`}
                                    />

                                    {isPaused && (
                                        <div
                                            className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
                                            <PauseRounded className="text-white text-4xl"/>
                                        </div>
                                    )}
                                </div>

                                <div className="ml-4 min-w-0">
                                    <p className="primary-color gotham-medium fade-in text-sm text-neutral-900 truncate mb-2">
                                        {track.artist}
                                    </p>
                                    <p className="gotham-black fade-in leading-none text-lg text-white truncate mb-0">
                                        {track.name}
                                    </p>
                                    <p className="gotham-bold fade-in text-gray-200 tracking-tight truncate">
                                        {track.album}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </>
                )
            }
        </Box>
    );
};
