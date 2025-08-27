import React, {useEffect, useState} from 'react';
import {useSelector} from 'react-redux';
import {RootState, store} from '@stores/stores';
import {useAudioPlayer} from 'react-use-audio-player';
import {removeSound, setCurrentSound} from '@modules/tmi/application/store/soundQueueSlice';

const FADE_DURATION = 500;
const CLEANUP_DELAY = 2000;

const SoundBox: React.FC = () => {
    const {load, stop, fade} = useAudioPlayer();

    const sounds = useSelector((state: RootState) => state.soundQueue.sounds);
    const currentSound = useSelector((state: RootState) => state.soundQueue.currentSound);

    const [isPlaying, setIsPlaying] = useState(false);

    useEffect(() => {
        if (!sounds.length || isPlaying) return;

        const nextSound = sounds[0];
        store.dispatch(setCurrentSound(nextSound));
        setIsPlaying(true);

        load(nextSound.url, {
            autoplay: true,
            initialVolume: nextSound.volume,
        });

        fade(0, 1, FADE_DURATION);

        setTimeout(() => {
            fade(1, 0, FADE_DURATION);
            setTimeout(() => stop(), FADE_DURATION);

            setTimeout(() => {
                setIsPlaying(false);
                store.dispatch(removeSound());
            }, CLEANUP_DELAY);

        }, nextSound.duration);
    }, [sounds, isPlaying, load, fade, stop]);

    if (!currentSound) return null;

    return (
        <>
            {isPlaying && (
                <div id="alert-box">
                    <div id="line">
                        <div id="box"></div>
                        <div id="mainText">{currentSound.url}</div>
                        <div id="subText">{currentSound.volume}</div>
                    </div>
                </div>
            )}
        </>
    );
};

export default SoundBox;
